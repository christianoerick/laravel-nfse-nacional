<?php

namespace ChristianoErick\NfseNacional\Xml;

/**
 * Leitura do resources/schema-map.php (gerado dos XSD por tools/generate.php).
 */
final class SchemaMap
{
    public const NAMESPACE = 'http://www.sped.fazenda.gov.br/nfse';

    /** @var array{roots: array<string, string>, types: array<string, array{attributes: list<string>, children: list<array{0: string, 1: ?string, 2: bool, 3: ?string}>}>}|null */
    private static ?array $map = null;

    /**
     * @return array{roots: array<string, string>, types: array<string, array{attributes: list<string>, children: list<array{0: string, 1: ?string, 2: bool, 3: ?string}>}>}
     */
    public static function map(): array
    {
        return self::$map ??= require dirname(__DIR__, 2).'/resources/schema-map.php';
    }

    public static function rootType(string $element): ?string
    {
        return self::map()['roots'][$element] ?? null;
    }

    /**
     * @return array{attributes: list<string>, children: list<array{0: string, 1: ?string, 2: bool, 3: ?string}>}|null
     */
    public static function type(string $type): ?array
    {
        return self::map()['types'][$type] ?? null;
    }

    /**
     * Tipo complexo de um filho (null = valor simples), ou false se o filho não existe.
     */
    public static function childType(string $type, string $child): string|false|null
    {
        foreach (self::type($type)['children'] ?? [] as [$name, $childType]) {
            if ($name === $child) {
                return $childType;
            }
        }

        return false;
    }

    public static function schemaPath(string $file = ''): string
    {
        return dirname(__DIR__, 2).'/resources/schemas'.($file !== '' ? '/'.$file : '');
    }
}
