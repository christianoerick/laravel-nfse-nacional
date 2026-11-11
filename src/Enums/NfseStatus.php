<?php

namespace ChristianoErick\NfseNacional\Enums;

use ChristianoErick\NfseNacional\Enums\Concerns\HasOptions;

/**
 * Situação da NFS-e (cStat).
 *
 * Gerado por tools/generate.php a partir de TStat (XSD oficial).
 */
enum NfseStatus: string
{
    use HasOptions;

    case Generated = '100';
    case JudicialDecision = '102';
    case Standalone = '103';
    case Mei = '107';

    public function label(): string
    {
        return match ($this) {
            self::Generated => 'NFS-e Gerada',
            self::JudicialDecision => 'NFS-e de Decisão Judicial',
            self::Standalone => 'NFS-e Avulsa',
            self::Mei => 'NFS-e MEI',
        };
    }
}
