<?php

namespace ChristianoErick\NfseNacional\Enums;

use ChristianoErick\NfseNacional\Enums\Concerns\HasOptions;

/**
 * Motivo da substituição (subst/cMotivo e e105102/cMotivo).
 *
 * Gerado por tools/generate.php a partir de TSCodJustSubst (XSD oficial).
 */
enum ReplacementReason: string
{
    use HasOptions;

    case SimplesExclusion = '01';
    case SimplesInclusion = '02';
    case RetroactiveImmunityInclusion = '03';
    case RetroactiveImmunityExclusion = '04';
    case RejectedByTakerOrIntermediary = '05';
    case Other = '99';

    public function label(): string
    {
        return match ($this) {
            self::SimplesExclusion => 'Desenquadramento de NFS-e do Simples Nacional',
            self::SimplesInclusion => 'Enquadramento de NFS-e no Simples Nacional',
            self::RetroactiveImmunityInclusion => 'Inclusão Retroativa de Imunidade/Isenção para NFS-e',
            self::RetroactiveImmunityExclusion => 'Exclusão Retroativa de Imunidade/Isenção para NFS-e',
            self::RejectedByTakerOrIntermediary => 'Rejeição de NFS-e pelo tomador ou pelo intermediário se responsável pelo recolhimento do tributo',
            self::Other => 'Outros',
        };
    }
}
