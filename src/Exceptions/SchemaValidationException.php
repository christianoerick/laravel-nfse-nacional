<?php

namespace ChristianoErick\NfseNacional\Exceptions;

/**
 * O XML não passou no XSD oficial. Nada foi enviado ao Sefin.
 */
class SchemaValidationException extends NfseException
{
    /**
     * @param  list<array{line: int, message: string}>  $errors
     */
    public function __construct(public readonly array $errors, public readonly string $xml)
    {
        parent::__construct('XML fora do leiaute oficial: '.implode(' | ', array_map(fn (array $error) => $error['message'], $errors)));
    }
}
