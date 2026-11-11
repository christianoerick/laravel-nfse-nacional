<?php

namespace ChristianoErick\NfseNacional\Enums;

use ChristianoErick\NfseNacional\Enums\Concerns\HasOptions;

/**
 * Tipo de documento fiscal referenciado (tipoChaveDFe).
 *
 * Gerado por tools/generate.php a partir de TSRTCTipoChaveDFe (XSD oficial).
 */
enum ReferencedDocumentType: string
{
    use HasOptions;

    case NfsE = '1';
    case NfE = '2';
    case CtE = '3';
    case Outro = '9';

    public function label(): string
    {
        return match ($this) {
            self::NfsE => 'NFS-e',
            self::NfE => 'NF-e',
            self::CtE => 'CT-e',
            self::Outro => 'Outro',
        };
    }
}
