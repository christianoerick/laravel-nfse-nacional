<?php

namespace ChristianoErick\NfseNacional\Enums;

use ChristianoErick\NfseNacional\Enums\Concerns\HasOptions;

/**
 * Categoria do veículo (pedágio).
 *
 * Gerado por tools/generate.php a partir de TSCategVeic (XSD oficial).
 */
enum VehicleCategory: string
{
    use HasOptions;

    case CategoriaDeVeiculosTipoNaoInformado = '00';
    case AutomovelCaminhoneteEFurgao = '01';
    case CaminhaoLeveOnibusCaminhaoTratorE = '02';
    case AutomovelECaminhoneteComSemireboque = '03';
    case CaminhaoCaminhaoTratorCaminhaoTratorCom = '04';
    case AutomovelECaminhoneteComReboque = '05';
    case CaminhaoComReboque = '06';
    case CaminhaoTratorComSemiReboque = '07';
    case MotocicletasMotonetasEBicicletasMotorizadas = '08';
    case VeiculoEspecial = '09';
    case VeiculoIsento = '10';

    public function label(): string
    {
        return match ($this) {
            self::CategoriaDeVeiculosTipoNaoInformado => 'Categoria de veículos (tipo não informado na nota de origem)',
            self::AutomovelCaminhoneteEFurgao => 'Automóvel, caminhonete e furgão',
            self::CaminhaoLeveOnibusCaminhaoTratorE => 'Caminhão leve, ônibus, caminhão trator e furgão',
            self::AutomovelECaminhoneteComSemireboque => 'Automóvel e caminhonete com semireboque',
            self::CaminhaoCaminhaoTratorCaminhaoTratorCom => 'Caminhão, caminhão-trator, caminhão-trator com semi-reboque e ônibus',
            self::AutomovelECaminhoneteComReboque => 'Automóvel e caminhonete com reboque',
            self::CaminhaoComReboque => 'Caminhão com reboque',
            self::CaminhaoTratorComSemiReboque => 'Caminhão trator com semi-reboque',
            self::MotocicletasMotonetasEBicicletasMotorizadas => 'Motocicletas, motonetas e bicicletas motorizadas',
            self::VeiculoEspecial => 'Veículo especial',
            self::VeiculoIsento => 'Veículo Isento',
        };
    }
}
