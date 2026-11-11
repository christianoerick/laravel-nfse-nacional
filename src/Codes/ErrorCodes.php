<?php

namespace ChristianoErick\NfseNacional\Codes;

/**
 * Mensagens oficiais dos códigos de rejeição/alerta da recepção da DPS
 * (Anexo I). O Sefin já manda a descrição; isto serve para documentar e
 * para quando só o código chega.
 */
final class ErrorCodes
{
    /** @var array<string, string>|null */
    private static ?array $codes = null;

    /**
     * @return array<string, string>
     */
    public static function all(): array
    {
        return self::$codes ??= require dirname(__DIR__, 2).'/resources/data/errors.php';
    }

    public static function message(?string $code): ?string
    {
        return $code === null ? null : (self::all()[strtoupper($code)] ?? null);
    }
}
