<?php

namespace ChristianoErick\NfseNacional\Enums;

use ChristianoErick\NfseNacional\Enums\Concerns\HasOptions;

/**
 * Não informar o total aproximado de tributos (totTrib/indTotTrib).
 *
 * Gerado por tools/generate.php a partir de TSTipoIndTotTrib (XSD oficial).
 */
enum TotalTaxIndicator: string
{
    use HasOptions;

    case DoNotInform = '0';

    public function label(): string
    {
        return match ($this) {
            self::DoNotInform => 'Não informar (Simples Nacional / Decreto 8.264/2014)',
        };
    }
}
