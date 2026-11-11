<?php

namespace ChristianoErick\NfseNacional\Support;

use Carbon\CarbonImmutable;
use ChristianoErick\NfseNacional\Enums\Environment;
use DateTimeInterface;

/**
 * Leiaute (pacote de XSD oficial) usado para montar e validar a DPS.
 *
 * - v1.01: esquemas de 09/02/2026 — produção até 30/11/2026.
 * - nt009: NT SE/CGNFS-e 009 v1.01, esquemas de 20/10/2026 — produção
 *   restrita desde 20/10/2026 e produção a partir de 01/12/2026.
 *
 * Em "auto" (padrão) a escolha é pela data e pelo ambiente, com as datas de
 * config('nfse-nacional.layout_switch').
 */
enum Layout: string
{
    case V101 = 'v1.01';
    case Nt009 = 'nt009';

    public const DEFAULT_SWITCH = [
        'production' => '2026-12-01',
        'restricted' => '2026-10-20',
    ];

    public static function resolve(self|string|null $configured, Environment $environment, ?DateTimeInterface $at = null, ?array $switch = null): self
    {
        if ($configured instanceof self) {
            return $configured;
        }

        if ($configured !== null && $configured !== '' && $configured !== 'auto') {
            return self::from($configured);
        }

        $switch ??= self::DEFAULT_SWITCH;
        $date = $switch[$environment->key()] ?? self::DEFAULT_SWITCH[$environment->key()];
        $at = CarbonImmutable::instance($at ?? new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')))->setTimezone('America/Sao_Paulo');

        return $at->toDateString() >= $date ? self::Nt009 : self::V101;
    }

    /**
     * Leiaute de produção hoje (para quem monta XML fora do cliente).
     */
    public static function current(): self
    {
        $config = function_exists('config') ? config('nfse-nacional') : null;

        return self::resolve($config['layout'] ?? 'auto', Environment::Production, null, $config['layout_switch'] ?? null);
    }

    public function label(): string
    {
        return match ($this) {
            self::V101 => 'Leiaute 1.01 (produção até 30/11/2026)',
            self::Nt009 => 'NT SE/CGNFS-e 009 v1.01',
        };
    }
}
