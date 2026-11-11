<?php

namespace ChristianoErick\NfseNacional\Enums;

use ChristianoErick\NfseNacional\Enums\Concerns\HasOptions;

/**
 * Vínculo com movimentação temporária de bens (comExt/movTempBens).
 *
 * Gerado por tools/generate.php a partir de TSMovTempBens (XSD oficial).
 */
enum TemporaryGoodsMovement: string
{
    use HasOptions;

    case DesconhecidoTipoNaoInformadoNaNota = '0';
    case Nao = '1';
    case VinculadaDeclaracaoDeImportacao = '2';
    case VinculadaDeclaracaoDeExportacao = '3';

    public function label(): string
    {
        return match ($this) {
            self::DesconhecidoTipoNaoInformadoNaNota => 'Desconhecido (tipo não informado na nota de origem)',
            self::Nao => 'Não',
            self::VinculadaDeclaracaoDeImportacao => 'Vinculada - Declaração de Importação',
            self::VinculadaDeclaracaoDeExportacao => 'Vinculada - Declaração de Exportação',
        };
    }
}
