<?php

namespace ChristianoErick\NfseNacional\Codes;

/**
 * Lista nacional de serviços (cTribNac, 6 dígitos: item 2 + subitem 2 +
 * desdobro 2), conforme o Anexo I do manual. Os códigos municipais
 * (cTribMun) são de cada prefeitura.
 */
final class ServiceCodes
{
    /** @var array<string, string>|null */
    private static ?array $codes = null;

    /**
     * @return array<string, string> código => descrição
     */
    public function all(): array
    {
        return self::$codes ??= require dirname(__DIR__, 2).'/resources/data/services.php';
    }

    public function description(string $code): ?string
    {
        return $this->all()[self::normalize($code)] ?? null;
    }

    public function exists(string $code): bool
    {
        return $this->description($code) !== null;
    }

    /**
     * Busca por código ("01.05", "0105") ou trecho da descrição.
     *
     * @return array<string, string>
     */
    public function search(string $term, int $limit = 30): array
    {
        $term = trim($term);
        $digits = preg_replace('/\D/', '', $term) ?? '';
        $needle = mb_strtolower($term);

        $found = array_filter(
            $this->all(),
            fn (string $description, string $code) => ($digits !== '' && str_starts_with($code, $digits))
                || ($digits !== '' && str_starts_with($code, str_pad($digits, 2, '0', STR_PAD_LEFT)))
                || str_contains(mb_strtolower($description), $needle),
            ARRAY_FILTER_USE_BOTH,
        );

        return array_slice($found, 0, $limit, true);
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    public function options(): array
    {
        return array_map(fn (string $code, string $description) => ['value' => $code, 'label' => self::format($code).' - '.$description], array_keys($this->all()), $this->all());
    }

    /** "010501" ou "1.05.01" → "010501". */
    public static function normalize(string $code): string
    {
        return str_pad(preg_replace('/\D/', '', $code) ?? '', 6, '0', STR_PAD_LEFT);
    }

    /** "010501" → "01.05.01". */
    public static function format(string $code): string
    {
        $code = self::normalize($code);

        return substr($code, 0, 2).'.'.substr($code, 2, 2).'.'.substr($code, 4, 2);
    }
}
