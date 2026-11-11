<?php

namespace ChristianoErick\NfseNacional\Support;

/**
 * CPF/CNPJ: limpeza, validação dos dígitos e máscara. CNPJ alfanumérico
 * (a partir de 2026) é aceito: letras valem ASCII - 48 no cálculo.
 */
final class Document
{
    public static function clean(?string $document): string
    {
        return strtoupper(preg_replace('/[^0-9A-Za-z]/', '', (string) $document) ?? '');
    }

    public static function isCpf(?string $document): bool
    {
        $cpf = self::clean($document);

        if (! preg_match('/^\d{11}$/', $cpf) || preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            $sum = 0;

            for ($i = 0; $i < $t; $i++) {
                $sum += (int) $cpf[$i] * (($t + 1) - $i);
            }

            if ((int) $cpf[$t] !== ((10 * $sum) % 11) % 10) {
                return false;
            }
        }

        return true;
    }

    public static function isCnpj(?string $document): bool
    {
        $cnpj = self::clean($document);

        if (! preg_match('/^[0-9A-Z]{12}\d{2}$/', $cnpj) || preg_match('/^(\d)\1{13}$/', $cnpj)) {
            return false;
        }

        foreach ([12, 13] as $length) {
            $weights = $length === 12 ? [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2] : [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
            $sum = 0;

            for ($i = 0; $i < $length; $i++) {
                $sum += (ord($cnpj[$i]) - 48) * $weights[$i];
            }

            $digit = $sum % 11 < 2 ? 0 : 11 - $sum % 11;

            if ((int) $cnpj[$length] !== $digit) {
                return false;
            }
        }

        return true;
    }

    public static function isValid(?string $document): bool
    {
        return self::isCpf($document) || self::isCnpj($document);
    }

    /** Chave do XML para o documento: CPF, CNPJ ou null. */
    public static function tag(?string $document): ?string
    {
        return match (strlen(self::clean($document))) {
            11 => 'CPF',
            14 => 'CNPJ',
            default => null,
        };
    }

    public static function format(?string $document): string
    {
        $value = self::clean($document);

        return match (strlen($value)) {
            11 => substr($value, 0, 3).'.'.substr($value, 3, 3).'.'.substr($value, 6, 3).'-'.substr($value, 9),
            14 => substr($value, 0, 2).'.'.substr($value, 2, 3).'.'.substr($value, 5, 3).'/'.substr($value, 8, 4).'-'.substr($value, 12),
            default => (string) $document,
        };
    }
}
