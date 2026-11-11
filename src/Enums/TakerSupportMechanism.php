<?php

namespace ChristianoErick\NfseNacional\Enums;

use ChristianoErick\NfseNacional\Enums\Concerns\HasOptions;

/**
 * Mecanismo de apoio/fomento ao comércio exterior do tomador (comExt/mecAFComexT).
 *
 * Gerado por tools/generate.php a partir de TSMecAFComExToma (XSD oficial).
 */
enum TakerSupportMechanism: string
{
    use HasOptions;

    case DesconhecidoTipoNaoInformadoNaNota = '00';
    case Nenhum = '01';
    case AdmPublicaEReprInternacional = '02';
    case AlugueisEArrendMercantilDeMaquinas = '03';
    case ArrendamentoMercantilDeAeronaveParaEmpresa = '04';
    case ComissaoAAgentesExternosNaExportacao = '05';
    case DespesasDeArmazenagemMovETransporte = '06';
    case EventosFifaSubsidiaria = '07';
    case EventosFifa = '08';
    case FretesArrendamentosDeEmbarcacoesOuAeronaves = '09';
    case MaterialAeronautico = '10';
    case PromocaoDeBensNoExterior = '11';
    case PromocaoDeDestTuristicosBrasileiros = '12';
    case PromocaoDoBrasilNoExterior = '13';
    case PromocaoServicosNoExterior = '14';
    case Recine = '15';
    case Recopa = '16';
    case RegistroEManutencaoDeMarcasPatentes = '17';
    case Reicomp = '18';
    case Reidi = '19';
    case Repenec = '20';
    case Repes = '21';
    case Retaero = '22';
    case Retid = '23';
    case RoyaltiesAssistenciaTecnicaCientificaEAssemelhados = '24';
    case ServicosDeAvaliacaoDaConformidadeVinculados = '25';
    case Zpe = '26';

    public function label(): string
    {
        return match ($this) {
            self::DesconhecidoTipoNaoInformadoNaNota => 'Desconhecido (tipo não informado na nota de origem)',
            self::Nenhum => 'Nenhum',
            self::AdmPublicaEReprInternacional => 'Adm. Pública e Repr. Internacional',
            self::AlugueisEArrendMercantilDeMaquinas => 'Alugueis e Arrend. Mercantil de maquinas, equip., embarc. e aeronaves',
            self::ArrendamentoMercantilDeAeronaveParaEmpresa => 'Arrendamento Mercantil de aeronave para empresa de transporte aéreo público',
            self::ComissaoAAgentesExternosNaExportacao => 'Comissão a agentes externos na exportação',
            self::DespesasDeArmazenagemMovETransporte => 'Despesas de armazenagem, mov. e transporte de carga no exterior',
            self::EventosFifaSubsidiaria => 'Eventos FIFA (subsidiária)',
            self::EventosFifa => 'Eventos FIFA',
            self::FretesArrendamentosDeEmbarcacoesOuAeronaves => 'Fretes, arrendamentos de embarcações ou aeronaves e outros',
            self::MaterialAeronautico => 'Material Aeronáutico',
            self::PromocaoDeBensNoExterior => 'Promoção de Bens no Exterior',
            self::PromocaoDeDestTuristicosBrasileiros => 'Promoção de Dest. Turísticos Brasileiros',
            self::PromocaoDoBrasilNoExterior => 'Promoção do Brasil no Exterior',
            self::PromocaoServicosNoExterior => 'Promoção Serviços no Exterior',
            self::Recine => 'RECINE',
            self::Recopa => 'RECOPA',
            self::RegistroEManutencaoDeMarcasPatentes => 'Registro e Manutenção de marcas, patentes e cultivares',
            self::Reicomp => 'REICOMP',
            self::Reidi => 'REIDI',
            self::Repenec => 'REPENEC',
            self::Repes => 'REPES',
            self::Retaero => 'RETAERO',
            self::Retid => 'RETID',
            self::RoyaltiesAssistenciaTecnicaCientificaEAssemelhados => 'Royalties, Assistência Técnica, Científica e Assemelhados',
            self::ServicosDeAvaliacaoDaConformidadeVinculados => 'Serviços de avaliação da conformidade vinculados aos Acordos da OMC',
            self::Zpe => 'ZPE',
        };
    }
}
