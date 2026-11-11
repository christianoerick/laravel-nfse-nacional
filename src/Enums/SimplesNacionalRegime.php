<?php

namespace ChristianoErick\NfseNacional\Enums;

use ChristianoErick\NfseNacional\Enums\Concerns\HasOptions;

/**
 * Regime de apuração dos tributos pelo Simples Nacional (regApTribSN).
 *
 * Gerado por tools/generate.php a partir de TSRegimeApuracaoSimpNac (XSD oficial).
 */
enum SimplesNacionalRegime: string
{
    use HasOptions;

    case AllBySimples = '1';
    case FederalBySimplesIssqnOutside = '2';
    case AllOutsideSimples = '3';

    public function label(): string
    {
        return match ($this) {
            self::AllBySimples => 'Regime de apuração dos tributos federais e municipal pelo SN',
            self::FederalBySimplesIssqnOutside => 'Regime de apuração dos tributos federais pelo SN e ISSQN por fora do SN conforme respectiva legislação municipal do tributo',
            self::AllOutsideSimples => 'Regime de apuração dos tributos federais e municipal por fora do SN conforme respectivas legislações federal e municipal de cada tributo',
        };
    }
}
