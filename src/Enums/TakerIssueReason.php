<?php

namespace ChristianoErick\NfseNacional\Enums;

use ChristianoErick\NfseNacional\Enums\Concerns\HasOptions;

/**
 * Motivo da emissão pelo tomador/intermediário (cMotivoEmisTI).
 *
 * Gerado por tools/generate.php a partir de TSMotivoEmisTI (XSD oficial).
 */
enum TakerIssueReason: string
{
    use HasOptions;

    case ImportacaoDeServico = '1';
    case TomadorIntermediarioObrigadoAEmitirNfs = '2';
    case TomadorIntermediarioEmitindoNfsEPor = '3';
    case TomadorIntermediarioEmitindoPorRejeitarA = '4';

    public function label(): string
    {
        return match ($this) {
            self::ImportacaoDeServico => 'Importação de Serviço',
            self::TomadorIntermediarioObrigadoAEmitirNfs => 'Tomador/Intermediário obrigado a emitir NFS-e por legislação municipal',
            self::TomadorIntermediarioEmitindoNfsEPor => 'Tomador/Intermediário emitindo NFS-e por recusa de emissão pelo prestador',
            self::TomadorIntermediarioEmitindoPorRejeitarA => 'Tomador/Intermediário emitindo por rejeitar a NFS-e emitida pelo prestador',
        };
    }
}
