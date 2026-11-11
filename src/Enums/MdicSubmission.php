<?php

namespace ChristianoErick\NfseNacional\Enums;

use ChristianoErick\NfseNacional\Enums\Concerns\HasOptions;

/**
 * Compartilhar com o MDIC (comExt/mdic).
 *
 * Gerado por tools/generate.php a partir de TSEnvMDIC (XSD oficial).
 */
enum MdicSubmission: string
{
    use HasOptions;

    case No = '0';
    case Yes = '1';

    public function label(): string
    {
        return match ($this) {
            self::No => 'Não enviar para o MDIC',
            self::Yes => 'Enviar para o MDIC',
        };
    }
}
