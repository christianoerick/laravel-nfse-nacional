<?php

namespace ChristianoErick\NfseNacional\Enums;

use ChristianoErick\NfseNacional\Enums\Concerns\HasOptions;

/**
 * Quem emite a DPS (tpEmit).
 *
 * Gerado por tools/generate.php a partir de TSEmitenteDPS (XSD oficial).
 */
enum IssuerRole: string
{
    use HasOptions;

    case Provider = '1';
    case Taker = '2';
    case Intermediary = '3';

    public function label(): string
    {
        return match ($this) {
            self::Provider => 'Prestador',
            self::Taker => 'Tomador',
            self::Intermediary => 'Intermediário',
        };
    }
}
