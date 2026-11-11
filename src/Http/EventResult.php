<?php

namespace ChristianoErick\NfseNacional\Http;

use ChristianoErick\NfseNacional\Xml\XmlReader;

/**
 * Evento registrado (XML do evento assinado pelo Sistema Nacional).
 */
final readonly class EventResult
{
    /**
     * @param  array<string, mixed>  $response
     */
    public function __construct(
        public string $eventXml,
        public string $signedRequestXml = '',
        public array $response = [],
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function data(): array
    {
        return $this->eventXml !== '' ? XmlReader::toArray($this->eventXml) : [];
    }

    public function processedAt(): ?string
    {
        return $this->response['dataHoraProcessamento'] ?? null;
    }
}
