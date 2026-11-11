<?php

namespace ChristianoErick\NfseNacional\Enums;

use ChristianoErick\NfseNacional\Enums\Concerns\HasOptions;

/**
 * Tipo do benefício municipal (BM/tpBM).
 *
 * Gerado por tools/generate.php a partir de TBMISSQN (XSD oficial).
 */
enum MunicipalBenefitType: string
{
    use HasOptions;

    case Code11 = '1';
    case Code22 = '2';
    case Code33 = '3';
    case Code44 = '4';

    public function label(): string
    {
        return match ($this) {
            self::Code11 => '1',
            self::Code22 => '2',
            self::Code33 => '3',
            self::Code44 => '4',
        };
    }
}
