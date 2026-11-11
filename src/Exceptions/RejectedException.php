<?php

namespace ChristianoErick\NfseNacional\Exceptions;

use ChristianoErick\NfseNacional\Support\ProcessingMessage;

/**
 * O Sefin/ADN recebeu e recusou (regra de negócio, 4xx com mensagens). Não
 * adianta reenviar igual: corrija os dados.
 */
class RejectedException extends NfseException
{
    /**
     * @param  list<ProcessingMessage>  $messages
     * @param  array<string, mixed>  $response
     */
    public function __construct(
        public readonly array $messages,
        public readonly int $status = 400,
        public readonly array $response = [],
    ) {
        parent::__construct(implode(' | ', array_map(fn (ProcessingMessage $m) => (string) $m, $messages)) ?: 'Requisição recusada pelo Sistema Nacional NFS-e.', $status);
    }

    /** Códigos das mensagens (ex.: ["E0039"]). */
    public function codes(): array
    {
        return array_values(array_filter(array_map(fn (ProcessingMessage $m) => $m->code, $this->messages)));
    }
}
