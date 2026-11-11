<?php

namespace ChristianoErick\NfseNacional\Exceptions;

/**
 * Falha técnica (rede, timeout, 429, 5xx). Vale tentar de novo — antes,
 * consulte a DPS (dpsExists/findByDps): a nota pode ter sido gerada e só a
 * resposta se perdeu.
 */
class ConnectionException extends NfseException
{
    public function __construct(string $message, public readonly ?int $status = null, ?\Throwable $previous = null)
    {
        parent::__construct($message, $status ?? 0, $previous);
    }
}
