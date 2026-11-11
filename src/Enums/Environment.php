<?php

namespace ChristianoErick\NfseNacional\Enums;

use ChristianoErick\NfseNacional\Enums\Concerns\HasOptions;

/**
 * Ambiente (tpAmb).
 *
 * Gerado por tools/generate.php a partir de TSTipoAmbiente (XSD oficial).
 */
enum Environment: string
{
    use HasOptions;

    case Production = '1';
    case Restricted = '2';

    public function label(): string
    {
        return match ($this) {
            self::Production => 'Produção',
            self::Restricted => 'Produção restrita (homologação)',
        };
    }

    /** Chave do config (urls.production / urls.restricted). */
    public function key(): string
    {
        return $this === self::Production ? 'production' : 'restricted';
    }

    /** Aceita "production"/"restricted" (ou "homologation"), 1/2 ou a própria instância. */
    public static function resolve(self|string|int|null $value): self
    {
        if ($value instanceof self) {
            return $value;
        }

        return match (strtolower((string) $value)) {
            '1', 'production', 'producao', 'produção' => self::Production,
            '2', 'restricted', 'homologation', 'homologacao', 'homologação', 'sandbox' => self::Restricted,
            default => throw new \InvalidArgumentException("Ambiente desconhecido: {$value}"),
        };
    }
}
