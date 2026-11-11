<?php

namespace ChristianoErick\NfseNacional\Enums\Concerns;

/**
 * Opções prontas para selects: [['value' => '1', 'label' => '...'], ...].
 */
trait HasOptions
{
    /**
     * @return list<array{value: string, label: string}>
     */
    public static function options(): array
    {
        return array_map(fn (self $case) => ['value' => $case->value, 'label' => $case->label()], self::cases());
    }

    /**
     * @return array<string, string> valor => rótulo
     */
    public static function labels(): array
    {
        return array_column(self::options(), 'label', 'value');
    }
}
