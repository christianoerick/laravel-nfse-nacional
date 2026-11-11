<?php

namespace ChristianoErick\NfseNacional\Enums;

use ChristianoErick\NfseNacional\Enums\Concerns\HasOptions;

/**
 * Retenção de PIS/COFINS/CSLL (piscofins/tpRetPisCofins).
 *
 * Gerado por tools/generate.php a partir de TSTipoRetPISCofins (XSD oficial).
 */
enum FederalWithholding: string
{
    use HasOptions;

    case PisCofinsCsllNaoRetidos = '0';
    case PisCofinsRetidos = '1';
    case PisCofinsNaoRetidos = '2';
    case PisCofinsCsllRetidos = '3';
    case PisCofinsRetidosCsllNaoRetido = '4';
    case PisRetidoCofinsCsllNaoRetido = '5';
    case CofinsRetidoPisCsllNaoRetido = '6';
    case PisNaoRetidoCofinsCsllRetidos = '7';
    case PisCofinsNaoRetidosCsllRetido = '8';
    case CofinsNaoRetidoPisCsllRetidos = '9';

    public function label(): string
    {
        return match ($this) {
            self::PisCofinsCsllNaoRetidos => 'PIS/COFINS/CSLL Não Retidos',
            self::PisCofinsRetidos => 'PIS/COFINS Retidos',
            self::PisCofinsNaoRetidos => 'PIS/COFINS Não Retidos',
            self::PisCofinsCsllRetidos => 'PIS/COFINS/CSLL Retidos',
            self::PisCofinsRetidosCsllNaoRetido => 'PIS/COFINS Retidos, CSLL Não Retido',
            self::PisRetidoCofinsCsllNaoRetido => 'PIS Retido, COFINS/CSLL Não Retido',
            self::CofinsRetidoPisCsllNaoRetido => 'COFINS Retido, PIS/CSLL Não Retido',
            self::PisNaoRetidoCofinsCsllRetidos => 'PIS Não Retido, COFINS/CSLL Retidos',
            self::PisCofinsNaoRetidosCsllRetido => 'PIS/COFINS Não Retidos, CSLL Retido',
            self::CofinsNaoRetidoPisCsllRetidos => 'COFINS Não Retido, PIS/CSLL Retidos',
        };
    }
}
