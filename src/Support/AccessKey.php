<?php

namespace ChristianoErick\NfseNacional\Support;

use ChristianoErick\NfseNacional\Exceptions\InvalidDataException;

/**
 * Chave de acesso da NFS-e (50 posições): município emissor (7) + ambiente
 * gerador (1) + tipo de inscrição (1) + inscrição (14) + número da NFS-e (13)
 * + ano/mês de emissão AAMM (4) + código numérico (9) + dígito verificador (1).
 */
final readonly class AccessKey
{
    private function __construct(
        public string $key,
        public string $city,
        public string $generatorEnvironment,
        public string $documentType,
        public string $document,
        public string $number,
        public string $yearMonth,
        public string $code,
        public string $checkDigit,
    ) {}

    public static function parse(string $key): self
    {
        $key = preg_replace('/^NFS/', '', trim($key)) ?? '';

        if (! preg_match('/^(\d{7})(\d)([12])([0-9A-Z]{14})(\d{13})(\d{4})(\d{9})(\d)$/', $key, $m)) {
            throw new InvalidDataException('Chave de acesso da NFS-e deve ter 50 posições.');
        }

        return new self($key, $m[1], $m[2], $m[3] === '1' ? 'CPF' : 'CNPJ', $m[3] === '1' ? substr($m[4], 3) : $m[4], ltrim($m[5], '0') ?: '0', $m[6], $m[7], $m[8]);
    }

    public static function isValid(string $key): bool
    {
        return (bool) preg_match('/^\d{7}\d[12][0-9A-Z]{14}\d{13}\d{4}\d{9}\d$/', $key);
    }

    /** Link do QR Code / consulta pública. */
    public function publicUrl(): string
    {
        return 'https://www.nfse.gov.br/ConsultaPublica?tpc=1&chave='.$this->key;
    }

    public function __toString(): string
    {
        return $this->key;
    }
}
