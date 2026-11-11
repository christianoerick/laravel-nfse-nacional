<?php

namespace ChristianoErick\NfseNacional\Enums;

use ChristianoErick\NfseNacional\Enums\Concerns\HasOptions;

/**
 * Tipo de operação com ente governamental (IBSCBS/tpOper).
 *
 * Gerado por tools/generate.php a partir de TSRTCTpOper (XSD oficial).
 */
enum GovernmentOperationType: string
{
    use HasOptions;

    case FornecimentoComPagamentoPosterior = '1';
    case RecebimentoDoPagamentoComFornecimentoJa = '2';
    case FornecimentoComPagamentoJaRealizado = '3';
    case RecebimentoDoPagamentoComFornecimentoPosterior = '4';
    case FornecimentoERecebimentoDoPagamentoConcomitantes = '5';

    public function label(): string
    {
        return match ($this) {
            self::FornecimentoComPagamentoPosterior => 'Fornecimento com pagamento posterior',
            self::RecebimentoDoPagamentoComFornecimentoJa => 'Recebimento do pagamento com fornecimento já realizado',
            self::FornecimentoComPagamentoJaRealizado => 'Fornecimento com pagamento já realizado',
            self::RecebimentoDoPagamentoComFornecimentoPosterior => 'Recebimento do pagamento com fornecimento posterior',
            self::FornecimentoERecebimentoDoPagamentoConcomitantes => 'Fornecimento e recebimento do pagamento concomitantes',
        };
    }
}
