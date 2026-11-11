<?php

namespace ChristianoErick\NfseNacional\Enums;

use ChristianoErick\NfseNacional\Enums\Concerns\HasOptions;

/**
 * Tipo de rodagem (pedágio).
 *
 * Gerado por tools/generate.php a partir de TSRodagem (XSD oficial).
 */
enum TollWheelType: string
{
    use HasOptions;

    case Simples = '1';
    case Dupla = '2';

    public function label(): string
    {
        return match ($this) {
            self::Simples => 'Simples',
            self::Dupla => 'Dupla',
        };
    }
}
