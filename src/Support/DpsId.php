<?php

namespace ChristianoErick\NfseNacional\Support;

use ChristianoErick\NfseNacional\Exceptions\InvalidDataException;

/**
 * Id da DPS (45 posições): "DPS" + município emissor (7) + tipo de
 * inscrição (1 = CPF, 2 = CNPJ) + inscrição com zeros à esquerda (14) +
 * série (5) + número (15). Aceita CNPJ alfanumérico.
 */
final class DpsId
{
    public static function make(string $cityIbgeCode, string $document, string|int $series, string|int $number): string
    {
        $document = Document::clean($document);

        if (! preg_match('/^\d{7}$/', $cityIbgeCode)) {
            throw new InvalidDataException('Código IBGE do município deve ter 7 dígitos.');
        }

        $type = match (strlen($document)) {
            11 => '1',
            14 => '2',
            default => throw new InvalidDataException('Documento do emitente deve ser CPF (11) ou CNPJ (14).'),
        };

        $number = (string) $number;

        if (! preg_match('/^[1-9]\d{0,14}$/', $number)) {
            throw new InvalidDataException('Número da DPS deve ser de 1 a 999999999999999.');
        }

        return 'DPS'.$cityIbgeCode.$type.str_pad($document, 14, '0', STR_PAD_LEFT).self::series($series).str_pad($number, 15, '0', STR_PAD_LEFT);
    }

    /**
     * Série com 5 dígitos. Aplicativo do contribuinte usa 00001 a 49999
     * (50000+ é reservado aos emissores públicos: 70000 web, 80000 app...).
     */
    public static function series(string|int $series): string
    {
        $digits = preg_replace('/\D/', '', (string) $series) ?? '';

        if ($digits === '' || (int) $digits < 1 || (int) $digits > 99999) {
            throw new InvalidDataException('Série da DPS deve ter de 1 a 5 dígitos.');
        }

        return str_pad((string) (int) $digits, 5, '0', STR_PAD_LEFT);
    }

    /**
     * @return array{city: string, document_type: string, document: string, series: string, number: string}
     */
    public static function parse(string $id): array
    {
        if (! preg_match('/^DPS(\d{7})([12])([0-9A-Z]{14})(\d{5})(\d{15})$/', $id, $m)) {
            throw new InvalidDataException('Id de DPS inválido.');
        }

        return [
            'city' => $m[1],
            'document_type' => $m[2] === '1' ? 'CPF' : 'CNPJ',
            'document' => $m[2] === '1' ? substr($m[3], 3) : $m[3],
            'series' => $m[4],
            'number' => ltrim($m[5], '0'),
        ];
    }
}
