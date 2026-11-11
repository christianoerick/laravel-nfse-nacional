<?php

namespace ChristianoErick\NfseNacional\Enums;

use ChristianoErick\NfseNacional\Enums\Concerns\HasOptions;

/**
 * Regime especial de tributação (regEspTrib).
 *
 * Gerado por tools/generate.php a partir de TSRegEspTrib (XSD oficial).
 */
enum SpecialTaxRegime: string
{
    use HasOptions;

    case Nenhum = '0';
    case AtoCooperadoCooperativa = '1';
    case Estimativa = '2';
    case MicroempresaMunicipal = '3';
    case NotarioOuRegistrador = '4';
    case ProfissionalAutonomo = '5';
    case SociedadeDeProfissionais = '6';
    case Outros = '9';

    public function label(): string
    {
        return match ($this) {
            self::Nenhum => 'Nenhum',
            self::AtoCooperadoCooperativa => 'Ato Cooperado (Cooperativa)',
            self::Estimativa => 'Estimativa',
            self::MicroempresaMunicipal => 'Microempresa Municipal',
            self::NotarioOuRegistrador => 'Notário ou Registrador',
            self::ProfissionalAutonomo => 'Profissional Autônomo',
            self::SociedadeDeProfissionais => 'Sociedade de Profissionais',
            self::Outros => 'Outros',
        };
    }
}
