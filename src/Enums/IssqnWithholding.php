<?php

namespace ChristianoErick\NfseNacional\Enums;

use ChristianoErick\NfseNacional\Enums\Concerns\HasOptions;

/**
 * Retenção do ISSQN (tpRetISSQN).
 *
 * Gerado por tools/generate.php a partir de TSTipoRetISSQN (XSD oficial).
 */
enum IssqnWithholding: string
{
    use HasOptions;

    case NotWithheld = '1';
    case WithheldByTaker = '2';
    case WithheldByIntermediary = '3';

    public function label(): string
    {
        return match ($this) {
            self::NotWithheld => 'Não Retido',
            self::WithheldByTaker => 'Retido pelo Tomador',
            self::WithheldByIntermediary => 'Retido pelo Intermediario',
        };
    }
}
