<?php

namespace ChristianoErick\NfseNacional\Xml;

use ChristianoErick\NfseNacional\Support\Layout;

/**
 * Leitura do resources/schema-map.php (gerado dos XSD por tools/generate.php).
 */
final class SchemaMap
{
    public const NAMESPACE = 'http://www.sped.fazenda.gov.br/nfse';

    /** @var array<string, array{roots: array<string, string>, types: array<string, array{attributes: list<string>, children: list<array{0: string, 1: ?string, 2: bool, 3: ?string}>}>}> */
    private static array $maps = [];

    /**
     * @return array{roots: array<string, string>, types: array<string, array{attributes: list<string>, children: list<array{0: string, 1: ?string, 2: bool, 3: ?string}>}>}
     */
    public static function map(?Layout $layout = null): array
    {
        $layout ??= Layout::current();

        return self::$maps[$layout->value] ??= require dirname(__DIR__, 2).'/resources/schema-map/'.$layout->value.'.php';
    }

    public static function rootType(string $element, ?Layout $layout = null): ?string
    {
        return self::map($layout)['roots'][$element] ?? null;
    }

    /**
     * @return array{attributes: list<string>, children: list<array{0: string, 1: ?string, 2: bool, 3: ?string}>}|null
     */
    public static function type(string $type, ?Layout $layout = null): ?array
    {
        return self::map($layout)['types'][$type] ?? null;
    }

    /**
     * Tipo complexo de um filho (null = valor simples), ou false se o filho não existe.
     */
    public static function childType(string $type, string $child, ?Layout $layout = null): string|false|null
    {
        foreach (self::type($type, $layout)['children'] ?? [] as [$name, $childType]) {
            if ($name === $child) {
                return $childType;
            }
        }

        return false;
    }

    public static function schemaPath(string $file = '', ?Layout $layout = null): string
    {
        $layout ??= Layout::current();

        return dirname(__DIR__, 2).'/resources/schemas/'.$layout->value.($file !== '' ? '/'.$file : '');
    }
}
