<?php

namespace ChristianoErick\NfseNacional\Certificate;

use Carbon\CarbonImmutable;
use ChristianoErick\NfseNacional\Exceptions\CertificateException;
use OpenSSLAsymmetricKey;
use SensitiveParameter;

/**
 * Certificado A1 (PKCS#12/.pfx) já aberto: certificado e chave só em
 * memória. Lê o titular (CNPJ/CPF) pelo padrão ICP-Brasil, a validade e o
 * emissor. Abre também .pfx com cifra legada (RC2-40), convertendo pelo
 * binário do OpenSSL com -legacy, sem gravar nada em disco.
 */
final class Certificate
{
    /** @var array<string, mixed> */
    private array $parsed;

    /**
     * @param  list<string>  $chain  certificados intermediários (PEM)
     */
    private function __construct(
        private readonly string $certificatePem,
        #[SensitiveParameter] private readonly string $privateKeyPem,
        private readonly array $chain = [],
    ) {
        $parsed = openssl_x509_parse($certificatePem);

        if ($parsed === false) {
            throw new CertificateException('Não foi possível ler o certificado.');
        }

        $this->parsed = $parsed;
    }

    public static function fromPfx(#[SensitiveParameter] string $pfx, #[SensitiveParameter] string $password, ?string $opensslBinary = null): self
    {
        if ($pfx === '') {
            throw new CertificateException('Arquivo do certificado vazio.');
        }

        if (openssl_pkcs12_read($pfx, $parts, $password)) {
            return self::fromParts($parts);
        }

        $pem = self::readLegacy($pfx, $password, $opensslBinary ?? self::configuredBinary());

        if ($pem === null) {
            throw new CertificateException('Senha incorreta ou arquivo .pfx inválido.');
        }

        preg_match_all('/-----BEGIN CERTIFICATE-----.+?-----END CERTIFICATE-----/s', $pem, $certificates);
        preg_match('/-----BEGIN (?:RSA |EC |ENCRYPTED )?PRIVATE KEY-----.+?-----END (?:RSA |EC |ENCRYPTED )?PRIVATE KEY-----/s', $pem, $key);

        $privateKey = $key ? openssl_pkey_get_private($key[0]) : false;

        if ($privateKey === false) {
            throw new CertificateException('Senha incorreta ou arquivo .pfx inválido.');
        }

        $leaf = null;
        $chain = [];

        foreach ($certificates[0] as $certificate) {
            if ($leaf === null && openssl_x509_check_private_key($certificate, $privateKey)) {
                $leaf = $certificate;
            } else {
                $chain[] = $certificate;
            }
        }

        if ($leaf === null) {
            throw new CertificateException('O .pfx não tem o certificado da chave privada.');
        }

        return new self($leaf, self::exportKey($privateKey), $chain);
    }

    public static function fromPath(string $path, #[SensitiveParameter] string $password, ?string $opensslBinary = null): self
    {
        $content = @file_get_contents($path);

        if ($content === false) {
            throw new CertificateException("Certificado não encontrado: {$path}");
        }

        return self::fromPfx($content, $password, $opensslBinary);
    }

    public static function fromPem(string $certificatePem, #[SensitiveParameter] string $privateKeyPem, array $chain = []): self
    {
        return new self($certificatePem, $privateKeyPem, $chain);
    }

    /**
     * @param  array{cert: string, pkey: string, extracerts?: list<string>}  $parts
     */
    private static function fromParts(array $parts): self
    {
        return new self($parts['cert'], $parts['pkey'], array_values($parts['extracerts'] ?? []));
    }

    // ------------------------------------------------------------ dados

    /** Nome do titular (CN sem o ":documento" do padrão ICP-Brasil). */
    public function holderName(): string
    {
        $cn = (string) ($this->parsed['subject']['CN'] ?? '');

        return trim((string) preg_replace('/:\s*[0-9A-Za-z]{11,14}$/', '', $cn));
    }

    /** CNPJ (14) ou CPF (11) do titular, só dígitos (CNPJ alfanumérico em maiúsculas). */
    public function document(): ?string
    {
        $cn = (string) ($this->parsed['subject']['CN'] ?? '');

        if (preg_match('/:\s*([0-9A-Za-z]{14}|[0-9]{11})$/', $cn, $match)) {
            return strtoupper($match[1]);
        }

        // e-CNPJ/e-CPF: OtherName 2.16.76.1.3.3 (CNPJ) ou 2.16.76.1.3.1 (CPF, a partir da 9ª posição).
        $san = (string) ($this->parsed['extensions']['subjectAltName'] ?? '');

        if (preg_match('/2\.16\.76\.1\.3\.3[^0-9A-Z]*([0-9A-Z]{14})/', $san, $match)) {
            return $match[1];
        }

        if (preg_match('/2\.16\.76\.1\.3\.1[^0-9]*[0-9]{8}([0-9]{11})/', $san, $match)) {
            return $match[1];
        }

        return null;
    }

    public function isCompany(): bool
    {
        return strlen((string) $this->document()) === 14;
    }

    public function validFrom(): CarbonImmutable
    {
        return CarbonImmutable::createFromTimestamp((int) $this->parsed['validFrom_time_t']);
    }

    public function validTo(): CarbonImmutable
    {
        return CarbonImmutable::createFromTimestamp((int) $this->parsed['validTo_time_t']);
    }

    public function isExpired(?CarbonImmutable $at = null): bool
    {
        return $this->validTo()->lessThanOrEqualTo($at ?? CarbonImmutable::now());
    }

    /** Dias inteiros até o vencimento (negativo se já venceu). */
    public function daysUntilExpiration(?CarbonImmutable $at = null): int
    {
        $at ??= CarbonImmutable::now();

        return (int) floor($at->diffInSeconds($this->validTo(), false) / 86400);
    }

    public function serialNumber(): string
    {
        return (string) ($this->parsed['serialNumberHex'] ?? $this->parsed['serialNumber'] ?? '');
    }

    public function issuerName(): string
    {
        return (string) ($this->parsed['issuer']['CN'] ?? '');
    }

    public function subject(): string
    {
        return (string) ($this->parsed['name'] ?? '');
    }

    /** SHA-1 do certificado (DER), hexadecimal minúsculo. */
    public function fingerprint(): string
    {
        return (string) openssl_x509_fingerprint($this->certificatePem, 'sha1');
    }

    // ------------------------------------------------------------ material

    public function certificatePem(): string
    {
        return $this->certificatePem;
    }

    /** Certificado + intermediários, para o mTLS. */
    public function certificateChainPem(): string
    {
        return implode("\n", [trim($this->certificatePem), ...array_map('trim', $this->chain)])."\n";
    }

    /** Certificado em base64 (DER), para o X509Certificate da assinatura. */
    public function certificateBase64(): string
    {
        return (string) preg_replace('/-----[A-Z ]+-----|\s+/', '', $this->certificatePem);
    }

    public function privateKey(): OpenSSLAsymmetricKey
    {
        $key = openssl_pkey_get_private($this->privateKeyPem);

        if ($key === false) {
            throw new CertificateException('Chave privada inválida.');
        }

        return $key;
    }

    public function privateKeyPem(): string
    {
        return $this->privateKeyPem;
    }

    /**
     * Regrava como .pfx no formato atual (AES-256), que o PHP abre sem o
     * provider legado. Útil para guardar o certificado já convertido.
     */
    public function toPfx(#[SensitiveParameter] string $password): string
    {
        if (! openssl_pkcs12_export($this->certificatePem, $pfx, $this->privateKey(), $password, ['extracerts' => $this->chain])) {
            throw new CertificateException('Não foi possível exportar o certificado.');
        }

        return $pfx;
    }

    /**
     * @return array{holder: string, document: ?string, valid_from: string, valid_to: string, serial: string, issuer: string, fingerprint: string}
     */
    public function toArray(): array
    {
        return [
            'holder' => $this->holderName(),
            'document' => $this->document(),
            'valid_from' => $this->validFrom()->toIso8601String(),
            'valid_to' => $this->validTo()->toIso8601String(),
            'serial' => $this->serialNumber(),
            'issuer' => $this->issuerName(),
            'fingerprint' => $this->fingerprint(),
        ];
    }

    public function __debugInfo(): array
    {
        return $this->toArray();
    }

    // ------------------------------------------------------------ interno

    private static function exportKey(OpenSSLAsymmetricKey $key): string
    {
        if (! openssl_pkey_export($key, $pem)) {
            throw new CertificateException('Não foi possível ler a chave privada.');
        }

        return $pem;
    }

    private static function configuredBinary(): string
    {
        return function_exists('config') ? (string) config('nfse-nacional.openssl_binary', 'openssl') : 'openssl';
    }

    /**
     * .pfx com cifra legada: o binário lê com -legacy e devolve PEM pela
     * saída padrão. A senha vai por variável de ambiente, nunca na linha de
     * comando.
     */
    private static function readLegacy(string $pfx, string $password, string $binary): ?string
    {
        if (! function_exists('proc_open')) {
            return null;
        }

        foreach ([['-legacy'], []] as $extra) {
            $command = [$binary, 'pkcs12', ...$extra, '-nodes', '-passin', 'env:NFSE_PFX_PASS'];
            $process = @proc_open($command, [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, null, [
                'NFSE_PFX_PASS' => $password,
                'PATH' => getenv('PATH') ?: '/usr/local/bin:/usr/bin:/bin',
            ]);

            if (! is_resource($process)) {
                return null;
            }

            fwrite($pipes[0], $pfx);
            fclose($pipes[0]);
            $output = stream_get_contents($pipes[1]);
            fclose($pipes[1]);
            stream_get_contents($pipes[2]);
            fclose($pipes[2]);

            if (proc_close($process) === 0 && is_string($output) && str_contains($output, 'PRIVATE KEY')) {
                return $output;
            }
        }

        return null;
    }
}
