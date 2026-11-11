<?php

namespace ChristianoErick\NfseNacional\Tests;

use ChristianoErick\NfseNacional\Certificate\Certificate;
use ChristianoErick\NfseNacional\NfseNacionalServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    public const PROVIDER_CNPJ = '11222333000181';

    public const TAKER_CNPJ = '11444777000161';

    public const CITY = '2704302';

    protected function getPackageProviders($app): array
    {
        return [NfseNacionalServiceProvider::class];
    }

    /** Certificado autoassinado no padrão ICP-Brasil (CN "NOME:CNPJ"). */
    protected function makePfx(string $password = 'secret', int $days = 365, string $cn = 'EMPRESA TESTE LTDA:'.self::PROVIDER_CNPJ): string
    {
        $key = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
        $csr = openssl_csr_new(['commonName' => $cn, 'countryName' => 'BR', 'organizationName' => 'ICP-Brasil'], $key, ['digest_alg' => 'sha256']);
        $x509 = openssl_csr_sign($csr, null, $key, $days, ['digest_alg' => 'sha256'], random_int(1, PHP_INT_MAX));
        openssl_pkcs12_export($x509, $pfx, $key, $password);

        return $pfx;
    }

    protected function certificate(int $days = 365): Certificate
    {
        return Certificate::fromPfx($this->makePfx('secret', $days), 'secret');
    }

    /**
     * DPS no formato da nota real de exemplo (Simples Nacional ME/EPP,
     * licenciamento de software, ISS não retido), com dados fictícios.
     *
     * @return array<string, mixed>
     */
    protected function dpsFields(array $overrides = []): array
    {
        return array_replace_recursive([
            'tpAmb' => 2,
            'dhEmi' => '2026-11-11T16:27:00-03:00',
            'verAplic' => 'Teste-1.0',
            'serie' => 1,
            'nDPS' => 1,
            'dCompet' => '2026-11-11',
            'tpEmit' => 1,
            'cLocEmi' => self::CITY,
            'prest' => [
                'CNPJ' => self::PROVIDER_CNPJ,
                'fone' => '82999990000',
                'email' => 'financeiro@example.com',
                'regTrib' => ['opSimpNac' => 3, 'regApTribSN' => 1, 'regEspTrib' => 0],
            ],
            'toma' => [
                'CNPJ' => self::TAKER_CNPJ,
                'xNome' => 'CLIENTE EXEMPLO LTDA',
                'end' => [
                    'endNac' => ['cMun' => self::CITY, 'CEP' => '57000000'],
                    'xLgr' => 'RUA EXEMPLO',
                    'nro' => '100',
                    'xCpl' => 'SALA 1',
                    'xBairro' => 'CENTRO',
                ],
            ],
            'serv' => [
                'locPrest' => ['cLocPrestacao' => self::CITY],
                'cServ' => ['cTribNac' => '010501', 'cTribMun' => '001', 'xDescServ' => 'Licenciamento de software.'],
            ],
            'valores' => [
                'vServPrest' => ['vServ' => 7500],
                'trib' => [
                    'tribMun' => ['tribISSQN' => 1, 'tpRetISSQN' => 1],
                    'tribFed' => ['piscofins' => ['CST' => '00', 'tpRetPisCofins' => 0]],
                    'totTrib' => ['pTotTribSN' => 6],
                ],
            ],
        ], $overrides);
    }

    protected function fixture(string $name): string
    {
        return (string) file_get_contents(__DIR__.'/Fixtures/'.$name);
    }
}
