<?php

namespace ChristianoErick\NfseNacional\Enums;

use ChristianoErick\NfseNacional\Enums\Concerns\HasOptions;

/**
 * Motivo da solicitação de análise fiscal para cancelamento (e101103/cMotivo).
 *
 * Gerado por tools/generate.php a partir de TSCodJustAnaliseFiscalCanc (XSD oficial).
 */
enum AnalysisCancellationReason: string
{
    use HasOptions;

    case IssueError = '1';
    case ServiceNotProvided = '2';
    case Other = '9';

    public function label(): string
    {
        return match ($this) {
            self::IssueError => 'Erro na Emissão',
            self::ServiceNotProvided => 'Serviço não Prestado',
            self::Other => 'Outros',
        };
    }
}
