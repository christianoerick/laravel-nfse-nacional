<?php

namespace ChristianoErick\NfseNacional\Enums;

use ChristianoErick\NfseNacional\Enums\Concerns\HasOptions;

/**
 * Tipo de suspensão da exigibilidade do ISSQN (exigSusp/tpSusp).
 *
 * Gerado por tools/generate.php a partir de TSOpExigSuspensa (XSD oficial).
 */
enum IssqnSuspension: string
{
    use HasOptions;

    case JudicialDecision = '1';
    case AdministrativeProceeding = '2';

    public function label(): string
    {
        return match ($this) {
            self::JudicialDecision => 'Exigibilidade Suspensa por Decisão Judicial',
            self::AdministrativeProceeding => 'Exigibilidade Suspensa por Processo Administrativo',
        };
    }
}
