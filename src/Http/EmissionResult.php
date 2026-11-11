<?php

namespace ChristianoErick\NfseNacional\Http;

use ChristianoErick\NfseNacional\Nfse\NfseDocument;
use ChristianoErick\NfseNacional\Support\ProcessingMessage;

/**
 * NFS-e gerada a partir da DPS enviada.
 */
final readonly class EmissionResult
{
    /**
     * @param  list<ProcessingMessage>  $alerts
     * @param  array<string, mixed>  $response
     */
    public function __construct(
        public NfseDocument $nfse,
        public string $signedDpsXml,
        public array $alerts = [],
        public array $response = [],
        public bool $recovered = false,
    ) {}

    public function accessKey(): string
    {
        return $this->nfse->accessKey();
    }

    public function number(): string
    {
        return $this->nfse->number();
    }

    public function xml(): string
    {
        return $this->nfse->xml();
    }
}
