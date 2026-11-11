<?php

namespace ChristianoErick\NfseNacional\Enums;

use ChristianoErick\NfseNacional\Enums\Concerns\HasOptions;

/**
 * Processo de emissão (procEmi).
 *
 * Gerado por tools/generate.php a partir de TSProcEmissao (XSD oficial).
 */
enum EmissionProcess: string
{
    use HasOptions;

    case EmissaoComAplicativoDoContribuinteVia = '1';
    case EmissaoComAplicativoDisponibilizadoPeloFisco = '2';
    case EmissaoComAplicativoDisponibilizadoPeloFisco3 = '3';

    public function label(): string
    {
        return match ($this) {
            self::EmissaoComAplicativoDoContribuinteVia => 'Emissão com aplicativo do contribuinte (via Web Service)',
            self::EmissaoComAplicativoDisponibilizadoPeloFisco => 'Emissão com aplicativo disponibilizado pelo fisco (Web)',
            self::EmissaoComAplicativoDisponibilizadoPeloFisco3 => 'Emissão com aplicativo disponibilizado pelo fisco (App)',
        };
    }
}
