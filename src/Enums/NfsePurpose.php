<?php

namespace ChristianoErick\NfseNacional\Enums;

use ChristianoErick\NfseNacional\Enums\Concerns\HasOptions;

/**
 * Finalidade da NFS-e (finNFSe; no leiaute 1.01 fica em IBSCBS, na NT 009 no infDPS).
 *
 * Gerado por tools/generate.php a partir de TSRTCFinNFSe (XSD oficial).
 */
enum NfsePurpose: string
{
    use HasOptions;

    case NfsERegular = '0';

    public function label(): string
    {
        return match ($this) {
            self::NfsERegular => 'NFS-e regular',
        };
    }
}
