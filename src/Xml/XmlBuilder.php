<?php

namespace ChristianoErick\NfseNacional\Xml;

use BackedEnum;
use ChristianoErick\NfseNacional\Exceptions\InvalidDataException;
use DateTimeInterface;
use DOMDocument;
use DOMElement;
use Stringable;

/**
 * Monta o XML a partir de um array com os nomes oficiais das tags.
 *
 * - A ordem dos filhos vem do XSD (não importa a ordem do array).
 * - Atributos com "@": ['@versao' => '1.01', 'infDPS' => ['@Id' => ...]].
 * - Listas (maxOccurs > 1) aceitam lista de arrays.
 * - null e '' são omitidos; true/false viram 1/0; enums viram o valor.
 * - DateTime vira AAAA-MM-DD ou AAAA-MM-DDThh:mm:ss±hh:mm conforme o campo;
 *   números em campos decimais saem com as casas do leiaute (7500 → 7500.00).
 * - Chave desconhecida lança exceção com as chaves aceitas naquele grupo.
 */
final class XmlBuilder
{
    public function build(string $root, array $data): DOMDocument
    {
        $type = SchemaMap::rootType($root) ?? throw new InvalidDataException("Elemento raiz desconhecido: {$root}");

        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;

        $element = $dom->createElementNS(SchemaMap::NAMESPACE, $root);
        $dom->appendChild($element);
        $this->fill($dom, $element, $type, $data, $root);

        return $dom;
    }

    public function toXml(string $root, array $data): string
    {
        return (string) $this->build($root, $data)->saveXML();
    }

    private function fill(DOMDocument $dom, DOMElement $element, string $type, array $data, string $path): void
    {
        $definition = SchemaMap::type($type) ?? throw new InvalidDataException("Tipo desconhecido no leiaute: {$type}");

        $known = [];

        foreach ($definition['attributes'] as $attribute) {
            $known['@'.$attribute] = true;
            $value = $data['@'.$attribute] ?? null;

            if ($value !== null && $value !== '') {
                $element->setAttribute($attribute, $this->scalar($value, null, $path.'/@'.$attribute));
            }
        }

        foreach ($definition['children'] as [$name, $childType, $isList, $format]) {
            $known[$name] = true;

            if (! array_key_exists($name, $data) || $data[$name] === null || $data[$name] === '' || $data[$name] === []) {
                continue;
            }

            $value = $data[$name];
            $items = $isList && is_array($value) && array_is_list($value) ? $value : [$value];

            foreach ($items as $item) {
                if ($item === null || $item === '') {
                    continue;
                }

                $childPath = $path.'/'.$name;

                if ($childType !== null) {
                    if (! is_array($item)) {
                        throw new InvalidDataException("{$childPath} é um grupo: informe um array.");
                    }

                    $child = $dom->createElementNS(SchemaMap::NAMESPACE, $name);
                    $element->appendChild($child);
                    $this->fill($dom, $child, $childType, $item, $childPath);

                    continue;
                }

                $child = $dom->createElementNS(SchemaMap::NAMESPACE, $name);
                $child->appendChild($dom->createTextNode($this->scalar($item, $format, $childPath)));
                $element->appendChild($child);
            }
        }

        $unknown = array_diff(array_keys($data), array_keys($known));

        if ($unknown !== []) {
            throw new InvalidDataException(sprintf(
                'Campo(s) fora do leiaute em %s: %s. Aceitos: %s.',
                $path,
                implode(', ', $unknown),
                implode(', ', array_keys($known)),
            ));
        }
    }

    private function scalar(mixed $value, ?string $format, string $path): string
    {
        if ($value instanceof BackedEnum) {
            return (string) $value->value;
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if ($value instanceof DateTimeInterface) {
            return $format === 'date' ? $value->format('Y-m-d') : $value->format('Y-m-d\TH:i:sP');
        }

        if ($format !== null && str_starts_with($format, 'decimal:') && (is_int($value) || is_float($value) || (is_string($value) && is_numeric($value)))) {
            return number_format((float) $value, (int) substr($format, 8), '.', '');
        }

        if (is_scalar($value) || $value instanceof Stringable) {
            return trim((string) $value);
        }

        throw new InvalidDataException("{$path} deve ser um valor simples.");
    }
}
