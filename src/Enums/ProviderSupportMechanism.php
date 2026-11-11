<?php

namespace ChristianoErick\NfseNacional\Enums;

use ChristianoErick\NfseNacional\Enums\Concerns\HasOptions;

/**
 * Mecanismo de apoio/fomento ao comércio exterior do prestador (comExt/mecAFComexP).
 *
 * Gerado por tools/generate.php a partir de TSMecAFComExPrest (XSD oficial).
 */
enum ProviderSupportMechanism: string
{
    use HasOptions;

    case DesconhecidoTipoNaoInformadoNaNota = '00';
    case Nenhum = '01';
    case AccAdiantamentoSobreContratoDeCambio = '02';
    case AceAdiantamentoSobreCambiaisEntreguesReducao = '03';
    case BndesEximPosEmbarqueServicos = '04';
    case BndesEximPreEmbarqueServicos = '05';
    case FgeFundoDeGarantiaAExportacao = '06';
    case ProexEqualizacao = '07';
    case ProexFinanciamento = '08';

    public function label(): string
    {
        return match ($this) {
            self::DesconhecidoTipoNaoInformadoNaNota => 'Desconhecido (tipo não informado na nota de origem)',
            self::Nenhum => 'Nenhum',
            self::AccAdiantamentoSobreContratoDeCambio => 'ACC - Adiantamento sobre Contrato de Câmbio – Redução a Zero do IR e do IOF',
            self::AceAdiantamentoSobreCambiaisEntreguesReducao => 'ACE – Adiantamento sobre Cambiais Entregues - Redução a Zero do IR e do IOF',
            self::BndesEximPosEmbarqueServicos => 'BNDES-Exim Pós-Embarque – Serviços',
            self::BndesEximPreEmbarqueServicos => 'BNDES-Exim Pré-Embarque - Serviços',
            self::FgeFundoDeGarantiaAExportacao => 'FGE - Fundo de Garantia à Exportação',
            self::ProexEqualizacao => 'PROEX - EQUALIZAÇÃO',
            self::ProexFinanciamento => 'PROEX - Financiamento',
        };
    }
}
