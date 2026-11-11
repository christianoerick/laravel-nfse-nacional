<?php

namespace ChristianoErick\NfseNacional\Enums;

use ChristianoErick\NfseNacional\Enums\Concerns\HasOptions;

/**
 * Ambiente que gerou a NFS-e (ambGer).
 *
 * Gerado por tools/generate.php a partir de TSAmbGeradorNFSe (XSD oficial).
 */
enum GeneratorEnvironment: string
{
    use HasOptions;

    case Municipality = '1';
    case National = '2';

    public function label(): string
    {
        return match ($this) {
            self::Municipality => 'Prefeitura',
            self::National => 'Sistema Nacional da NFS-e',
        };
    }
}
