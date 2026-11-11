<?php

namespace ChristianoErick\NfseNacional\Console;

use ChristianoErick\NfseNacional\NfseNacional;
use Illuminate\Console\Command;

/**
 * Mostra titular, documento e validade de um .pfx (senha pedida se não vier).
 */
class CertificateCommand extends Command
{
    protected $signature = 'nfse-nacional:certificate {path : Caminho do .pfx} {--password= : Senha (se omitida, é perguntada)}';

    protected $description = 'Lê um certificado A1 (.pfx) e mostra titular, CNPJ/CPF e validade';

    public function handle(NfseNacional $nfse): int
    {
        $password = (string) ($this->option('password') ?? $this->secret('Senha do certificado'));
        $certificate = $nfse->certificateFromPath((string) $this->argument('path'), $password);

        $this->table(['Campo', 'Valor'], [
            ['Titular', $certificate->holderName()],
            ['Documento', $certificate->document() ?? '-'],
            ['Válido de', $certificate->validFrom()->format('d/m/Y H:i')],
            ['Válido até', $certificate->validTo()->format('d/m/Y H:i')],
            ['Dias restantes', (string) $certificate->daysUntilExpiration()],
            ['Emissor', $certificate->issuerName()],
            ['Série', $certificate->serialNumber()],
            ['SHA-1', $certificate->fingerprint()],
        ]);

        return $certificate->isExpired() ? self::FAILURE : self::SUCCESS;
    }
}
