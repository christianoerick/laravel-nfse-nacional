<?php

namespace ChristianoErick\NfseNacional\Enums;

use ChristianoErick\NfseNacional\Enums\Concerns\HasOptions;

/**
 * Destinatário é o próprio adquirente (IBSCBS/indDest).
 *
 * Gerado por tools/generate.php a partir de TSRTCIndDest (XSD oficial).
 */
enum RecipientIndicator: string
{
    use HasOptions;

    case ODestinatarioEOProprioTomador = '0';
    case ODestinatarioNaoEOProprio = '1';

    public function label(): string
    {
        return match ($this) {
            self::ODestinatarioEOProprioTomador => 'o destinatário é o próprio tomador/adquirente identificado na NFS-e (tomador = adquirente = destinatário)',
            self::ODestinatarioNaoEOProprio => 'o destinatário não é o próprio adquirente, podendo ser outra pessoa, física ou jurídica (ou equiparada), ou um estabelecimento diferente do indicado como tomador (tomador = adquirente ≠ destinatário)',
        };
    }
}
