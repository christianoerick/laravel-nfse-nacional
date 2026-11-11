<?php

namespace ChristianoErick\NfseNacional\Enums;

use ChristianoErick\NfseNacional\Enums\Concerns\HasOptions;

/**
 * Motivo de não informar o NIF do estrangeiro (cNaoNIF).
 *
 * Gerado por tools/generate.php a partir de TSCodNaoNIF (XSD oficial).
 */
enum NoNifReason: string
{
    use HasOptions;

    case NaoInformadoNaNotaDeOrigem = '0';
    case DispensadoDoNif = '1';
    case NaoExigenciaDoNif = '2';

    public function label(): string
    {
        return match ($this) {
            self::NaoInformadoNaNotaDeOrigem => 'Não informado na nota de origem',
            self::DispensadoDoNif => 'Dispensado do NIF',
            self::NaoExigenciaDoNif => 'Não exigência do NIF',
        };
    }
}
