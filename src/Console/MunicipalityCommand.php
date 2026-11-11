<?php

namespace ChristianoErick\NfseNacional\Console;

use ChristianoErick\NfseNacional\NfseNacional;
use Illuminate\Console\Command;

/**
 * Consulta (só leitura) o convênio do município com o Sistema Nacional:
 * diz se dá para emitir pela API do Sefin Nacional. Também testa o mTLS.
 */
class MunicipalityCommand extends Command
{
    protected $signature = 'nfse-nacional:municipality {ibge : Código IBGE do município (7 dígitos)}
        {--pfx= : Caminho do .pfx usado no mTLS}
        {--password= : Senha do certificado (se omitida, é perguntada)}
        {--environment= : production ou restricted}';

    protected $description = 'Consulta o convênio do município com o Sistema Nacional NFS-e (testa também o certificado/mTLS)';

    public function handle(NfseNacional $nfse): int
    {
        $path = (string) ($this->option('pfx') ?? $this->ask('Caminho do .pfx'));
        $password = (string) ($this->option('password') ?? $this->secret('Senha do certificado'));
        $client = $nfse->client($nfse->certificateFromPath($path, $password), $this->option('environment'));

        $agreement = $client->municipalAgreement((string) $this->argument('ibge'));

        $this->table(['Parâmetro', 'Valor'], collect($agreement->parameters)->map(fn ($value, $key) => [$key, is_bool($value) ? ($value ? 'sim' : 'não') : (string) json_encode($value)])->values()->all());
        $this->line($agreement->usesNationalIssuer()
            ? '<info>Emite pelo Sefin Nacional (emissores públicos/API).</info>'
            : '<comment>Município não usa o emissor nacional: a emissão pela API do Sefin será recusada.</comment>');

        return self::SUCCESS;
    }
}
