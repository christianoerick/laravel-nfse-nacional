<?php

namespace ChristianoErick\NfseNacional\Enums;

use ChristianoErick\NfseNacional\Enums\Concerns\HasOptions;

/**
 * Operação com consumidor final (IBSCBS/indFinal).
 *
 * Gerado por tools/generate.php a partir de TSRTCIndFinal (XSD oficial).
 */
enum FinalConsumer: string
{
    use HasOptions;

    case No = '0';
    case Yes = '1';

    public function label(): string
    {
        return match ($this) {
            self::No => 'Não',
            self::Yes => 'Sim',
        };
    }
}
