<?php

namespace ChristianoErick\NfseNacional\Enums;

use ChristianoErick\NfseNacional\Enums\Concerns\HasOptions;

/**
 * Motivo da rejeição da NFS-e pelo prestador/tomador/intermediário (e202205, e203206, e204207/cMotivo).
 *
 * Gerado por tools/generate.php a partir de TSCodMotivoRejeicao (XSD oficial).
 */
enum RejectionReason: string
{
    use HasOptions;

    case Duplicate = '1';
    case AlreadyIssuedByTaker = '2';
    case NoTaxableEvent = '3';
    case TaxResponsibilityError = '4';
    case ValueOrServiceError = '5';
    case Other = '9';

    public function label(): string
    {
        return match ($this) {
            self::Duplicate => 'NFS-e em duplicidade',
            self::AlreadyIssuedByTaker => 'NFS-e já emitida pelo tomador',
            self::NoTaxableEvent => 'Não ocorrência do fato gerador',
            self::TaxResponsibilityError => 'Erro quanto a responsabilidade tributária',
            self::ValueOrServiceError => 'Erro quanto ao valor do serviço, valor das deduções ou serviço prestado ou data do fato gerador',
            self::Other => 'Outros',
        };
    }
}
