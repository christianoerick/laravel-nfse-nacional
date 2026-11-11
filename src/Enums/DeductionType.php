<?php

namespace ChristianoErick\NfseNacional\Enums;

use ChristianoErick\NfseNacional\Enums\Concerns\HasOptions;

/**
 * Tipo de documento de dedução/redução (leiaute 1.01: docDedRed/tpDedRed).
 *
 * Gerado por tools/generate.php a partir de TSIdeDedRed (XSD oficial).
 */
enum DeductionType: string
{
    use HasOptions;

    case AlimentacaoEBebidasFrigobar = '1';
    case Materiais = '2';
    case ProducaoExterna = '3';
    case ReembolsoDeDespesas = '4';
    case RepasseConsorciado = '5';
    case RepassePlanoDeSaude = '6';
    case Servicos = '7';
    case SubempreitadaDeMaoDeObra = '8';
    case ProfissionalParceiro = '9';
    case OutrasDeducoes = '99';

    public function label(): string
    {
        return match ($this) {
            self::AlimentacaoEBebidasFrigobar => 'Alimentação e bebidas/frigobar',
            self::Materiais => 'Materiais',
            self::ProducaoExterna => 'Produção Externa',
            self::ReembolsoDeDespesas => 'Reembolso de despesas',
            self::RepasseConsorciado => 'Repasse consorciado',
            self::RepassePlanoDeSaude => 'Repasse plano de saúde',
            self::Servicos => 'Serviços',
            self::SubempreitadaDeMaoDeObra => 'Subempreitada de mão de obra',
            self::ProfissionalParceiro => 'Profissional parceiro',
            self::OutrasDeducoes => 'Outras deduções',
        };
    }
}
