<?php

namespace ChristianoErick\NfseNacional\Enums;

use ChristianoErick\NfseNacional\Enums\Concerns\HasOptions;

/**
 * Motivo do cancelamento (e101101/cMotivo).
 *
 * Gerado por tools/generate.php a partir de TSCodJustCanc (XSD oficial).
 */
enum CancellationReason: string
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
