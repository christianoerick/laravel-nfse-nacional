<?php

namespace ChristianoErick\NfseNacional\Enums;

use ChristianoErick\NfseNacional\Enums\Concerns\HasOptions;

/**
 * Modo de prestação no comércio exterior (comExt/mdPrestacao).
 *
 * Gerado por tools/generate.php a partir de TSModoPrestacao (XSD oficial).
 */
enum ServiceSupplyMode: string
{
    use HasOptions;

    case DesconhecidoTipoNaoInformadoNaNota = '0';
    case Transfronteirico = '1';
    case ConsumoNoBrasil = '2';
    case PresencaComercialNoExterior = '3';
    case MovimentoTemporarioDePessoasFisicas = '4';

    public function label(): string
    {
        return match ($this) {
            self::DesconhecidoTipoNaoInformadoNaNota => 'Desconhecido (tipo não informado na nota de origem)',
            self::Transfronteirico => 'Transfronteiriço',
            self::ConsumoNoBrasil => 'Consumo no Brasil',
            self::PresencaComercialNoExterior => 'Presença Comercial no Exterior',
            self::MovimentoTemporarioDePessoasFisicas => 'Movimento Temporário de Pessoas Físicas',
        };
    }
}
