<?php

namespace ChristianoErick\NfseNacional\Enums;

use ChristianoErick\NfseNacional\Enums\Concerns\HasOptions;

/**
 * Tipo de imunidade do ISSQN (tpImunidade).
 *
 * Gerado por tools/generate.php a partir de TSTipoImunidadeISSQN (XSD oficial).
 */
enum ImmunityType: string
{
    use HasOptions;

    case ImunidadeTipoNaoInformadoNaNota = '0';
    case PatrimonioRendaOuServicosUnsDos = '1';
    case TemplosDeQualquerCultoCf88Art = '2';
    case PatrimonioRendaOuServicosDosPartidos = '3';
    case LivrosJornaisPeriodicosEOPapel = '4';
    case FonogramasEVideofonogramasMusicaisProduzidosNo = '5';

    public function label(): string
    {
        return match ($this) {
            self::ImunidadeTipoNaoInformadoNaNota => 'Imunidade (tipo não informado na nota de origem)',
            self::PatrimonioRendaOuServicosUnsDos => 'Patrimônio, renda ou serviços, uns dos outros (CF88, Art 150, VI, a)',
            self::TemplosDeQualquerCultoCf88Art => 'Templos de qualquer culto (CF88, Art 150, VI, b)',
            self::PatrimonioRendaOuServicosDosPartidos => 'Patrimônio, renda ou serviços dos partidos políticos, inclusive suas fundações, das entidades sindicais dos trabalhadores, das instituições de educação e de assistência social, sem fins lucrativos, atendidos os requisitos da lei (CF88, Art 150, VI, c)',
            self::LivrosJornaisPeriodicosEOPapel => 'Livros, jornais, periódicos e o papel destinado a sua impressão (CF88, Art 150, VI, d)',
            self::FonogramasEVideofonogramasMusicaisProduzidosNo => 'Fonogramas e videofonogramas musicais produzidos no Brasil contendo obras musicais ou literomusicais de autores brasileiros e/ou obras em geral interpretadas por artistas brasileiros bem como os suportes materiais ou arquivos digitais que os contenham, salvo na etapa de replicação industrial de mídias ópticas de leitura a laser. (CF88, Art 150, VI, e)',
        };
    }
}
