<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Ambiente padrão
    |--------------------------------------------------------------------------
    |
    | "production" (tpAmb = 1) ou "restricted" (produção restrita/homologação,
    | tpAmb = 2). Cada chamada pode trocar com ->environment().
    |
    */
    'environment' => env('NFSE_NACIONAL_ENVIRONMENT', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Leiaute da DPS
    |--------------------------------------------------------------------------
    |
    | "auto" (padrão): escolhe pelo ambiente e pela data em layout_switch.
    | "v1.01": esquemas de 09/02/2026 (produção até 30/11/2026).
    | "nt009": NT SE/CGNFS-e 009 v1.01 (esquemas de 20/10/2026).
    |
    */
    'layout' => env('NFSE_NACIONAL_LAYOUT', 'auto'),

    // A partir de que dia cada ambiente usa a NT 009 (AAAA-MM-DD).
    'layout_switch' => [
        'production' => env('NFSE_NACIONAL_NT009_PRODUCTION', '2026-12-01'),
        'restricted' => env('NFSE_NACIONAL_NT009_RESTRICTED', '2026-10-20'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Endereços oficiais
    |--------------------------------------------------------------------------
    |
    | sefin: emissão, consulta da NFS-e e da DPS, eventos.
    | adn: DANFSe (/danfse), parâmetros municipais (/parametrizacao) e
    | distribuição de documentos ao contribuinte (/contribuintes).
    |
    */
    'urls' => [
        'production' => [
            'sefin' => 'https://sefin.nfse.gov.br/SefinNacional',
            'adn' => 'https://adn.nfse.gov.br',
        ],
        'restricted' => [
            'sefin' => 'https://sefin.producaorestrita.nfse.gov.br/SefinNacional',
            'adn' => 'https://adn.producaorestrita.nfse.gov.br',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Identificação do aplicativo (verAplic, até 20 caracteres)
    |--------------------------------------------------------------------------
    */
    'application_version' => env('NFSE_NACIONAL_APP_VERSION', 'LaravelNfseNacional1'),

    /*
    |--------------------------------------------------------------------------
    | Assinatura XMLDSig
    |--------------------------------------------------------------------------
    |
    | O manual (Anexo I) pede RSA-SHA1 com C14N; o Sefin também aceita
    | RSA-SHA256 (é o que o próprio Emissor Nacional usa).
    |
    */
    'signature_algorithm' => env('NFSE_NACIONAL_SIGNATURE', 'sha1'),

    /*
    |--------------------------------------------------------------------------
    | Validar a DPS no XSD antes de enviar
    |--------------------------------------------------------------------------
    |
    | Erro de leiaute vira exceção local (com o campo exato) em vez de
    | rejeição do Sefin.
    |
    */
    'validate_schema' => true,

    /*
    |--------------------------------------------------------------------------
    | HTTP
    |--------------------------------------------------------------------------
    |
    | verify: true (CAs do sistema), false (não recomendado) ou o caminho de
    | um bundle de CAs.
    |
    */
    'http' => [
        'timeout' => (int) env('NFSE_NACIONAL_TIMEOUT', 60),
        'connect_timeout' => 15,
        'verify' => env('NFSE_NACIONAL_CA_BUNDLE', true),
        'retries' => 2,
        'retry_sleep_ms' => 1000,
    ],

    /*
    |--------------------------------------------------------------------------
    | Pasta temporária para o par certificado/chave do mTLS
    |--------------------------------------------------------------------------
    |
    | Os arquivos são criados com permissão 0600 e apagados ao fim de cada
    | requisição. Null = sys_get_temp_dir().
    |
    */
    'temp_path' => env('NFSE_NACIONAL_TEMP_PATH'),

    /*
    |--------------------------------------------------------------------------
    | Binário do OpenSSL
    |--------------------------------------------------------------------------
    |
    | Usado só para abrir .pfx com cifra legada (RC2-40, comum em A1 antigo),
    | que o OpenSSL 3 do PHP não lê sem o provider "legacy".
    |
    */
    'openssl_binary' => env('NFSE_NACIONAL_OPENSSL', 'openssl'),

];
