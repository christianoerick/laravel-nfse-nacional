<?php

namespace ChristianoErick\NfseNacional\Enums;

use ChristianoErick\NfseNacional\Enums\Concerns\HasOptions;

/**
 * Tipo de ente governamental (IBSCBS/tpEnteGov).
 *
 * Gerado por tools/generate.php a partir de TSRTCTpEnteGov (XSD oficial).
 */
enum GovernmentEntity: string
{
    use HasOptions;

    case Uniao = '1';
    case Estado = '2';
    case DistritoFederal = '3';
    case Municipio = '4';

    public function label(): string
    {
        return match ($this) {
            self::Uniao => 'União',
            self::Estado => 'Estado',
            self::DistritoFederal => 'Distrito Federal',
            self::Municipio => 'Município',
        };
    }
}
