<?php

namespace ChristianoErick\NfseNacional\Enums;

use ChristianoErick\NfseNacional\Enums\Concerns\HasOptions;

/**
 * Unidade da federação (UF).
 *
 * Gerado por tools/generate.php a partir de TSUF (XSD oficial).
 */
enum State: string
{
    use HasOptions;

    case Ac = 'AC';
    case Al = 'AL';
    case Am = 'AM';
    case Ap = 'AP';
    case Ba = 'BA';
    case Ce = 'CE';
    case Df = 'DF';
    case Es = 'ES';
    case Go = 'GO';
    case Ma = 'MA';
    case Mg = 'MG';
    case Ms = 'MS';
    case Mt = 'MT';
    case Pa = 'PA';
    case Pb = 'PB';
    case Pe = 'PE';
    case Pi = 'PI';
    case Pr = 'PR';
    case Rj = 'RJ';
    case Rn = 'RN';
    case Ro = 'RO';
    case Rr = 'RR';
    case Rs = 'RS';
    case Sc = 'SC';
    case Se = 'SE';
    case Sp = 'SP';
    case To = 'TO';

    public function label(): string
    {
        return match ($this) {
            self::Ac => 'AC',
            self::Al => 'AL',
            self::Am => 'AM',
            self::Ap => 'AP',
            self::Ba => 'BA',
            self::Ce => 'CE',
            self::Df => 'DF',
            self::Es => 'ES',
            self::Go => 'GO',
            self::Ma => 'MA',
            self::Mg => 'MG',
            self::Ms => 'MS',
            self::Mt => 'MT',
            self::Pa => 'PA',
            self::Pb => 'PB',
            self::Pe => 'PE',
            self::Pi => 'PI',
            self::Pr => 'PR',
            self::Rj => 'RJ',
            self::Rn => 'RN',
            self::Ro => 'RO',
            self::Rr => 'RR',
            self::Rs => 'RS',
            self::Sc => 'SC',
            self::Se => 'SE',
            self::Sp => 'SP',
            self::To => 'TO',
        };
    }
}
