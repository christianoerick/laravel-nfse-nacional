<?php

namespace ChristianoErick\NfseNacional\Xml;

use ChristianoErick\NfseNacional\Exceptions\SchemaValidationException;
use ChristianoErick\NfseNacional\Support\Layout;
use DOMDocument;

/**
 * Valida contra os XSD oficiais (resources/schemas), localmente.
 */
final class SchemaValidator
{
    private readonly Layout $layout;

    public function __construct(?Layout $layout = null)
    {
        $this->layout = $layout ?? Layout::current();
    }

    private const SCHEMAS = [
        'DPS' => 'DPS_v1.01.xsd',
        'NFSe' => 'NFSe_v1.01.xsd',
        'pedRegEvento' => 'pedRegEvento_v1.01.xsd',
        'evento' => 'evento_v1.01.xsd',
    ];

    /**
     * @throws SchemaValidationException
     */
    public function validate(string|DOMDocument $xml): void
    {
        $errors = $this->errors($xml);

        if ($errors !== []) {
            throw new SchemaValidationException($errors, is_string($xml) ? $xml : (string) $xml->saveXML());
        }
    }

    /**
     * @return list<array{line: int, message: string}>
     */
    public function errors(string|DOMDocument $xml): array
    {
        $dom = is_string($xml) ? XmlReader::load($xml) : $xml;
        $root = $dom->documentElement?->localName;
        $schema = self::SCHEMAS[$root] ?? null;

        if ($schema === null) {
            return [['line' => 0, 'message' => "Sem XSD para o elemento raiz {$root}."]];
        }

        // Revalida a partir do texto: o DOM montado em memória não tem os
        // números de linha nem a normalização de namespace do parser.
        $copy = new DOMDocument;
        $copy->loadXML((string) $dom->saveXML(), LIBXML_NONET);

        $previous = libxml_use_internal_errors(true);
        libxml_clear_errors();
        $copy->schemaValidate(SchemaMap::schemaPath($schema, $this->layout));

        $errors = array_map(fn (\LibXMLError $error) => [
            'line' => $error->line,
            'message' => $this->translate(trim($error->message)),
        ], libxml_get_errors());

        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        return $errors;
    }

    /** Deixa a mensagem do libxml legível (sem o namespace inteiro). */
    private function translate(string $message): string
    {
        return str_replace(['{'.SchemaMap::NAMESPACE.'}', 'Element ', 'This element is not expected. Expected is one of', 'This element is not expected. Expected is', 'Missing child element(s). Expected is one of', 'Missing child element(s). Expected is'], ['', 'Elemento ', 'fora de ordem ou não permitido aqui. Esperado um de', 'fora de ordem ou não permitido aqui. Esperado', 'falta filho obrigatório. Esperado um de', 'falta filho obrigatório. Esperado'], $message);
    }
}
