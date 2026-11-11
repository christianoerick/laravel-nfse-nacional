<?php

namespace ChristianoErick\NfseNacional\Enums;

use ChristianoErick\NfseNacional\Enums\Concerns\HasOptions;

/**
 * Tipo de emissão da NFS-e (tpEmis).
 *
 * Gerado por tools/generate.php a partir de TSTipoEmissao (XSD oficial).
 */
enum EmissionType: string
{
    use HasOptions;

    case EmissaoNormalNoModeloDaNfs = '1';
    case EmissaoOriginalEmLeiauteProprioDo = '2';

    public function label(): string
    {
        return match ($this) {
            self::EmissaoNormalNoModeloDaNfs => 'Emissão normal no modelo da NFS-e Nacional',
            self::EmissaoOriginalEmLeiauteProprioDo => 'Emissão original em leiaute próprio do município com transcrição para o modelo da NFS-e Nacional',
        };
    }
}
