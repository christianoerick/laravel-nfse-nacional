<?php

namespace ChristianoErick\NfseNacional\Enums;

use ChristianoErick\NfseNacional\Enums\Concerns\HasOptions;

/**
 * Situação tributária do PIS/COFINS (piscofins/CST).
 *
 * Gerado por tools/generate.php a partir de TSTipoCST (XSD oficial).
 */
enum PisCofinsCst: string
{
    use HasOptions;

    case Nenhum = '00';
    case OperacaoTributavelComAliquotaBasica = '01';
    case OperacaoTributavelComAliquotaDiferenciada = '02';
    case OperacaoTributavelComAliquotaPorUnidade = '03';
    case OperacaoTributavelMonofasicaRevendaAAliquota = '04';
    case OperacaoTributavelPorSubstituicaoTributaria = '05';
    case OperacaoTributavelAAliquotaZero = '06';
    case OperacaoIsentaDaContribuicao = '07';
    case OperacaoSemIncidenciaDaContribuicao = '08';
    case OperacaoComSuspensaoDaContribuicao = '09';
    case OutrasOperacoesDeSaida = '49';
    case OperacaoComDireitoACreditoVinculada = '50';
    case OperacaoComDireitoACreditoVinculada51 = '51';
    case OperacaoComDireitoACreditoVinculada52 = '52';
    case OperacaoComDireitoACreditoVinculada53 = '53';
    case OperacaoComDireitoACreditoVinculada54 = '54';
    case OperacaoComDireitoACreditoVinculada55 = '55';
    case OperacaoComDireitoACreditoVinculada56 = '56';
    case CreditoPresumidoOperacaoDeAquisicaoVinculada = '60';
    case CreditoPresumidoOperacaoDeAquisicaoVinculada61 = '61';
    case CreditoPresumidoOperacaoDeAquisicaoVinculada62 = '62';
    case CreditoPresumidoOperacaoDeAquisicaoVinculada63 = '63';
    case CreditoPresumidoOperacaoDeAquisicaoVinculada64 = '64';
    case CreditoPresumidoOperacaoDeAquisicaoVinculada65 = '65';
    case CreditoPresumidoOperacaoDeAquisicaoVinculada66 = '66';
    case CreditoPresumidoOutrasOperacoes = '67';
    case OperacaoDeAquisicaoSemDireitoA = '70';
    case OperacaoDeAquisicaoComIsencao = '71';
    case OperacaoDeAquisicaoComSuspensao = '72';
    case OperacaoDeAquisicaoAAliquotaZero = '73';
    case OperacaoDeAquisicaoSemIncidenciaDa = '74';
    case OperacaoDeAquisicaoPorSubstituicaoTributaria = '75';
    case OutrasOperacoesDeEntrada = '98';
    case OutrasOperacoes = '99';

    public function label(): string
    {
        return match ($this) {
            self::Nenhum => 'Nenhum',
            self::OperacaoTributavelComAliquotaBasica => 'Operação Tributável com Alíquota Básica',
            self::OperacaoTributavelComAliquotaDiferenciada => 'Operação Tributável com Alíquota Diferenciada',
            self::OperacaoTributavelComAliquotaPorUnidade => 'Operação Tributável com Alíquota por Unidade de Medida de Produto',
            self::OperacaoTributavelMonofasicaRevendaAAliquota => 'Operação Tributável monofásica - Revenda a Alíquota Zero',
            self::OperacaoTributavelPorSubstituicaoTributaria => 'Operação Tributável por Substituição Tributária',
            self::OperacaoTributavelAAliquotaZero => 'Operação Tributável a Alíquota Zero',
            self::OperacaoIsentaDaContribuicao => 'Operação Isenta da Contribuição',
            self::OperacaoSemIncidenciaDaContribuicao => 'Operação sem Incidência da Contribuição',
            self::OperacaoComSuspensaoDaContribuicao => 'Operação com Suspensão da Contribuição',
            self::OutrasOperacoesDeSaida => 'Outras Operações de Saída',
            self::OperacaoComDireitoACreditoVinculada => 'Operação com Direito a Crédito – Vinculada Exclusivamente a Receita Tributada no Mercado Interno',
            self::OperacaoComDireitoACreditoVinculada51 => 'Operação com Direito a Crédito – Vinculada Exclusivamente a Receita Não-Tributada no Mercado Interno',
            self::OperacaoComDireitoACreditoVinculada52 => 'Operação com Direito a Crédito – Vinculada Exclusivamente a Receita de Exportação',
            self::OperacaoComDireitoACreditoVinculada53 => 'Operação com Direito a Crédito – Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno',
            self::OperacaoComDireitoACreditoVinculada54 => 'Operação com Direito a Crédito – Vinculada a Receitas Tributadas no Mercado Interno e de Exportação',
            self::OperacaoComDireitoACreditoVinculada55 => 'Operação com Direito a Crédito – Vinculada a Receitas Não Tributadas no Mercado Interno e de Exportação',
            self::OperacaoComDireitoACreditoVinculada56 => 'Operação com Direito a Crédito – Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno e de Exportação',
            self::CreditoPresumidoOperacaoDeAquisicaoVinculada => 'Crédito Presumido – Operação de Aquisição Vinculada Exclusivamente a Receita Tributada no Mercado Interno',
            self::CreditoPresumidoOperacaoDeAquisicaoVinculada61 => 'Crédito Presumido – Operação de Aquisição Vinculada Exclusivamente a Receita Não-Tributada no Mercado Interno',
            self::CreditoPresumidoOperacaoDeAquisicaoVinculada62 => 'Crédito Presumido – Operação de Aquisição Vinculada Exclusivamente a Receita de Exportação',
            self::CreditoPresumidoOperacaoDeAquisicaoVinculada63 => 'Crédito Presumido – Operação de Aquisição Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno',
            self::CreditoPresumidoOperacaoDeAquisicaoVinculada64 => 'Crédito Presumido – Operação de Aquisição Vinculada a Receitas Tributadas no Mercado Interno e de Exportação',
            self::CreditoPresumidoOperacaoDeAquisicaoVinculada65 => 'Crédito Presumido – Operação de Aquisição Vinculada a Receitas Não-Tributadas no Mercado Interno e de Exportação',
            self::CreditoPresumidoOperacaoDeAquisicaoVinculada66 => 'Crédito Presumido – Operação de Aquisição Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno e de Exportação',
            self::CreditoPresumidoOutrasOperacoes => 'Crédito Presumido – Outras Operações',
            self::OperacaoDeAquisicaoSemDireitoA => 'Operação de Aquisição sem Direito a Crédito',
            self::OperacaoDeAquisicaoComIsencao => 'Operação de Aquisição com Isenção',
            self::OperacaoDeAquisicaoComSuspensao => 'Operação de Aquisição com Suspensão',
            self::OperacaoDeAquisicaoAAliquotaZero => 'Operação de Aquisição a Alíquota Zero',
            self::OperacaoDeAquisicaoSemIncidenciaDa => 'Operação de Aquisição sem Incidência da Contribuição',
            self::OperacaoDeAquisicaoPorSubstituicaoTributaria => 'Operação de Aquisição por Substituição Tributária',
            self::OutrasOperacoesDeEntrada => 'Outras Operações de Entrada',
            self::OutrasOperacoes => 'Outras Operações',
        };
    }
}
