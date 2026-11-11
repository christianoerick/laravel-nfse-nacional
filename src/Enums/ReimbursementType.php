<?php

namespace ChristianoErick\NfseNacional\Enums;

use ChristianoErick\NfseNacional\Enums\Concerns\HasOptions;

/**
 * Tipo de reembolso, repasse ou ressarcimento (IBSCBS/tpReeRepRes).
 *
 * Gerado por tools/generate.php a partir de TSRTCTpReeRepRes (XSD oficial).
 */
enum ReimbursementType: string
{
    use HasOptions;

    case RepasseDeRemuneracaoPorIntermediacaoDe = '01';
    case RepasseDeValoresAFornecedorRelativo = '02';
    case ReembolsoOuRessarcimentoRecebidoPorAgencia = '03';
    case ReembolsoOuRessarcimentoRecebidoPorAgencia04 = '04';
    case OutrosReembolsosOuRessarcimentosRecebidosPor = '99';

    public function label(): string
    {
        return match ($this) {
            self::RepasseDeRemuneracaoPorIntermediacaoDe => 'Repasse de remuneração por intermediação de imóveis a demais corretores envolvidos na operação',
            self::RepasseDeValoresAFornecedorRelativo => 'Repasse de valores a fornecedor relativo a fornecimento intermediado por agência de turismo',
            self::ReembolsoOuRessarcimentoRecebidoPorAgencia => 'Reembolso ou ressarcimento recebido por agência de propaganda e publicidade por valores pagos relativos',
            self::ReembolsoOuRessarcimentoRecebidoPorAgencia04 => 'Reembolso ou ressarcimento recebido por agência de propaganda e publicidade por valores pagos relativos',
            self::OutrosReembolsosOuRessarcimentosRecebidosPor => 'Outros reembolsos ou ressarcimentos recebidos por valores pagos relativos a operações por conta e ordem de terceiro',
        };
    }
}
