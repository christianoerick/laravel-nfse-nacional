<?php

namespace ChristianoErick\NfseNacional\Enums;

use ChristianoErick\NfseNacional\Enums\Concerns\HasOptions;

/**
 * Situação perante o Simples Nacional (opSimpNac).
 *
 * Gerado por tools/generate.php a partir de TSOpSimpNac (XSD oficial).
 */
enum SimplesNacionalOption: string
{
    use HasOptions;

    case NotOptant = '1';
    case Mei = '2';
    case MeEpp = '3';

    public function label(): string
    {
        return match ($this) {
            self::NotOptant => 'Não Optante',
            self::Mei => 'Optante - Microempreendedor Individual (MEI)',
            self::MeEpp => 'Optante - Microempresa ou Empresa de Pequeno Porte (ME/EPP)',
        };
    }
}
