<?php

namespace ChristianoErick\NfseNacional\Http;

use ChristianoErick\NfseNacional\Certificate\Certificate;
use ChristianoErick\NfseNacional\Exceptions\CertificateException;

/**
 * O cURL só aceita o certificado do mTLS por arquivo: grava o PEM do
 * certificado (com a cadeia) e o da chave em arquivos 0600, apagados quando
 * o objeto sai de escopo.
 */
final class TemporaryCredentials
{
    public readonly string $certificatePath;

    public readonly string $keyPath;

    public function __construct(Certificate $certificate, ?string $directory = null)
    {
        $directory ??= sys_get_temp_dir();
        $this->certificatePath = $this->write($directory, 'nfse-crt-', $certificate->certificateChainPem());
        $this->keyPath = $this->write($directory, 'nfse-key-', $certificate->privateKeyPem());
    }

    public function __destruct()
    {
        foreach ([$this->certificatePath ?? null, $this->keyPath ?? null] as $path) {
            if ($path !== null && is_file($path)) {
                @unlink($path);
            }
        }
    }

    private function write(string $directory, string $prefix, string $content): string
    {
        $path = tempnam($directory, $prefix);

        if ($path === false) {
            throw new CertificateException("Não foi possível criar arquivo temporário em {$directory}.");
        }

        chmod($path, 0600);
        file_put_contents($path, $content);

        return $path;
    }
}
