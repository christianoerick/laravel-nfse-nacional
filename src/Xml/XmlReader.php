<?php

namespace ChristianoErick\NfseNacional\Xml;

use ChristianoErick\NfseNacional\Exceptions\InvalidDataException;
use DOMDocument;
use DOMElement;

/**
 * XML → array no mesmo formato aceito pelo XmlBuilder (atributos com "@",
 * filhos repetidos viram lista). A assinatura (Signature) é ignorada.
 */
final class XmlReader
{
    public static function load(string $xml): DOMDocument
    {
        $dom = new DOMDocument;
        $previous = libxml_use_internal_errors(true);
        $loaded = $dom->loadXML($xml, LIBXML_NONET | LIBXML_NOBLANKS);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (! $loaded || $dom->documentElement === null) {
            throw new InvalidDataException('XML inválido.');
        }

        return $dom;
    }

    /**
     * @return array<string, mixed>
     */
    public static function toArray(string|DOMDocument $xml): array
    {
        $dom = is_string($xml) ? self::load($xml) : $xml;

        return [$dom->documentElement->localName => self::element($dom->documentElement)];
    }

    private static function element(DOMElement $element): array|string
    {
        $result = [];

        foreach ($element->attributes as $attribute) {
            if ($attribute->prefix === 'xmlns' || $attribute->nodeName === 'xmlns') {
                continue;
            }

            $result['@'.$attribute->localName] = $attribute->value;
        }

        $counts = [];

        foreach ($element->childNodes as $child) {
            if ($child instanceof DOMElement && $child->localName !== 'Signature') {
                $counts[$child->localName] = ($counts[$child->localName] ?? 0) + 1;
            }
        }

        foreach ($element->childNodes as $child) {
            if (! $child instanceof DOMElement || $child->localName === 'Signature') {
                continue;
            }

            if ($counts[$child->localName] > 1) {
                $result[$child->localName][] = self::element($child);
            } else {
                $result[$child->localName] = self::element($child);
            }
        }

        if ($counts === [] && $result === []) {
            return $element->textContent;
        }

        return $result;
    }
}
