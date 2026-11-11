<?php

namespace ChristianoErick\NfseNacional\Enums;

use ChristianoErick\NfseNacional\Enums\Concerns\HasOptions;

/**
 * Tributação do ISSQN (tribISSQN).
 *
 * Gerado por tools/generate.php a partir de TSTribISSQN (XSD oficial).
 */
enum IssqnTaxation: string
{
    use HasOptions;

    case Taxable = '1';
    case Immunity = '2';
    case ServiceExport = '3';
    case NonIncidence = '4';

    public function label(): string
    {
        return match ($this) {
            self::Taxable => 'Operação tributável',
            self::Immunity => 'Imunidade',
            self::ServiceExport => 'Exportação de serviço',
            self::NonIncidence => 'Não Incidência',
        };
    }
}
