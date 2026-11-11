<?php

namespace ChristianoErick\NfseNacional\Enums;

use ChristianoErick\NfseNacional\Enums\Concerns\HasOptions;

/**
 * Tipo de ajuste da base de cálculo (NT 009: vAjusteBC/docAjusteBC/tpAjusteBC).
 *
 * Gerado por tools/generate.php a partir de TSRTCTpAjusteBC (XSD oficial).
 */
enum BaseAdjustmentType: string
{
    use HasOptions;

    case Materiais = '2';
    case RepasseConsorciado = '5';
    case RepassePlanoDeSaude = '6';
    case Servicos = '7';
    case SubempreitadaDeMaoDeObra = '8';
    case ProfissionalParceiro = '9';
    case OutrasDeducoesExclusivasDoIssqn = '99';
    case RepasseDeRemuneracaoPorIntermediacaoDe = '101';
    case RepasseDeValoresAFornecedorRelativo = '102';
    case ReembolsoOuRessarcimentoRecebidoPorAgencia = '103';
    case ReembolsoOuRessarcimentoRecebidoPorAgencia104 = '104';
    case GlosaDeServicosDeSaude = '105';
    case OutrosReembolsosOuRessarcimentosRecebidosPor = '199';

    public function label(): string
    {
        return match ($this) {
            self::Materiais => 'Materiais',
            self::RepasseConsorciado => 'Repasse consorciado',
            self::RepassePlanoDeSaude => 'Repasse plano de saúde',
            self::Servicos => 'Serviços',
            self::SubempreitadaDeMaoDeObra => 'Subempreitada de mão de obra',
            self::ProfissionalParceiro => 'Profissional parceiro',
            self::OutrasDeducoesExclusivasDoIssqn => 'Outras deduções exclusivas do ISSQN',
            self::RepasseDeRemuneracaoPorIntermediacaoDe => 'Repasse de remuneração por intermediação de imóveis a demais corretores envolvidos na operação',
            self::RepasseDeValoresAFornecedorRelativo => 'Repasse de valores a fornecedor relativo a fornecimento intermediado por agência de turismo',
            self::ReembolsoOuRessarcimentoRecebidoPorAgencia => 'Reembolso ou ressarcimento recebido por agência de propaganda e publicidade por valores pagos relativos a serviços de produção externa por conta e ordem de terceiro',
            self::ReembolsoOuRessarcimentoRecebidoPorAgencia104 => 'Reembolso ou ressarcimento recebido por agência de propaganda e publicidade por valores pagos relativos a serviços de mídia por conta e ordem de terceiro',
            self::GlosaDeServicosDeSaude => 'Glosa de Serviços de Saúde',
            self::OutrosReembolsosOuRessarcimentosRecebidosPor => 'Outros reembolsos ou ressarcimentos recebidos por valores pagos relativos a operações por conta e ordem de terceiro',
        };
    }
}
