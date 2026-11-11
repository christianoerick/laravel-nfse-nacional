<?php

namespace ChristianoErick\NfseNacional\Enums;

use ChristianoErick\NfseNacional\Enums\Concerns\HasOptions;

/**
 * Código do evento.
 *
 * Gerado por tools/generate.php a partir de TSCodigoEventoNFSe (XSD oficial).
 */
enum EventCode: string
{
    use HasOptions;

    case Cancellation = 'e101101';
    case CancellationBySubstitution = 'e105102';
    case CancellationApprovedByAnalysis = 'e105104';
    case CancellationDeniedByAnalysis = 'e105105';
    case CancellationByOffice = 'e305101';

    public function label(): string
    {
        return match ($this) {
            self::Cancellation => 'Cancelamento de NFS-e',
            self::CancellationBySubstitution => 'Cancelamento de NFS-e por substituição',
            self::CancellationApprovedByAnalysis => 'Cancelamento deferido por análise fiscal',
            self::CancellationDeniedByAnalysis => 'Cancelamento indeferido por análise fiscal',
            self::CancellationByOffice => 'Cancelamento de NFS-e por ofício',
        };
    }
}
