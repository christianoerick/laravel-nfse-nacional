<?php

namespace ChristianoErick\NfseNacional\Enums;

use ChristianoErick\NfseNacional\Enums\Concerns\HasOptions;

/**
 * Vínculo entre as partes no comércio exterior (comExt/vincPrest).
 *
 * Gerado por tools/generate.php a partir de TSVincPrest (XSD oficial).
 */
enum ForeignTradeLink: string
{
    use HasOptions;

    case SemVinculoComOTomadorPrestador = '0';
    case Controlada = '1';
    case Controladora = '2';
    case Coligada = '3';
    case Matriz = '4';
    case FilialOuSucursal = '5';
    case OutroVinculo = '6';
    case DesconhecidoTipoNaoInformadoNaNota = '9';

    public function label(): string
    {
        return match ($this) {
            self::SemVinculoComOTomadorPrestador => 'Sem vínculo com o Tomador/Prestador',
            self::Controlada => 'Controlada',
            self::Controladora => 'Controladora',
            self::Coligada => 'Coligada',
            self::Matriz => 'Matriz',
            self::FilialOuSucursal => 'Filial ou sucursal',
            self::OutroVinculo => 'Outro vínculo',
            self::DesconhecidoTipoNaoInformadoNaNota => 'Desconhecido (tipo não informado na nota de origem)',
        };
    }
}
