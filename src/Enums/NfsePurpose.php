<?php

namespace ChristianoErick\NfseNacional\Enums;

use ChristianoErick\NfseNacional\Enums\Concerns\HasOptions;

/**
 * Finalidade da NFS-e (IBSCBS/finNFSe).
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
