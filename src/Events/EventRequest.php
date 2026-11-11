<?php

namespace ChristianoErick\NfseNacional\Events;

use ChristianoErick\NfseNacional\Enums\CancellationReason;
use ChristianoErick\NfseNacional\Enums\Environment;
use ChristianoErick\NfseNacional\Enums\EventCode;
use ChristianoErick\NfseNacional\Enums\ReplacementReason;
use ChristianoErick\NfseNacional\Exceptions\InvalidDataException;
use ChristianoErick\NfseNacional\Support\AccessKey;
use ChristianoErick\NfseNacional\Support\Document;
use DateTimeInterface;

/**
 * Pedido de registro de evento (pedRegEvento). Atalhos para os eventos do
 * emitente; qualquer outro evento do leiaute vai por make() com o grupo
 * (ex.: 'e202201' => [...]) e os nomes oficiais das tags.
 */
final class EventRequest
{
    public const VERSION = '1.01';

    /**
     * @param  array<string, mixed>  $detail  grupo do evento (sem xDesc, que é fixo por evento)
     */
    private function __construct(
        public readonly string $accessKey,
        public readonly string $code,
        private readonly array $detail,
        private ?string $author = null,
    ) {
        if (! AccessKey::isValid($accessKey)) {
            throw new InvalidDataException('Chave de acesso da NFS-e inválida.');
        }
    }

    /**
     * @param  string  $code  código do grupo, com ou sem "e" (ex.: 101101 ou e101101)
     * @param  array<string, mixed>  $detail
     */
    public static function make(string $accessKey, string $code, array $detail): self
    {
        return new self($accessKey, ltrim($code, 'e'), $detail);
    }

    /** Cancelamento pelo emitente (e101101). */
    public static function cancellation(string $accessKey, CancellationReason|string|int $reason, string $description): self
    {
        return new self($accessKey, '101101', [
            'xDesc' => 'Cancelamento de NFS-e',
            'cMotivo' => $reason,
            'xMotivo' => $description,
        ]);
    }

    /**
     * Cancelamento por substituição (e105102): a nota original é cancelada
     * apontando para a nova, já emitida.
     */
    public static function substitution(string $accessKey, string $replacementAccessKey, ReplacementReason|string|int $reason, ?string $description = null): self
    {
        return new self($accessKey, '105102', array_filter([
            'xDesc' => 'Cancelamento de NFS-e por Substituição',
            'cMotivo' => $reason,
            'xMotivo' => $description,
            'chSubstituta' => $replacementAccessKey,
        ], fn ($value) => $value !== null && $value !== ''));
    }

    /** CNPJ/CPF do autor (padrão: o do certificado usado no envio). */
    public function author(string $document): self
    {
        $this->author = Document::clean($document);

        return $this;
    }

    public function eventCode(): ?EventCode
    {
        return EventCode::tryFrom('e'.$this->code);
    }

    public function id(): string
    {
        // Leiaute 1.01: "PRE" + chave (50) + código do evento (6) = 59 posições.
        return 'PRE'.$this->accessKey.$this->code;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(Environment $environment, string $applicationVersion, ?string $authorDocument = null, ?DateTimeInterface $at = null): array
    {
        $author = $this->author ?? Document::clean($authorDocument);

        if (Document::tag($author) === null) {
            throw new InvalidDataException('Informe o CNPJ ou CPF do autor do evento.');
        }

        $at ??= \Carbon\CarbonImmutable::now('America/Sao_Paulo')->subMinute();

        return [
            '@versao' => self::VERSION,
            'infPedReg' => [
                '@Id' => $this->id(),
                'tpAmb' => $environment,
                'verAplic' => $applicationVersion,
                'dhEvento' => $at,
                Document::tag($author).'Autor' => $author,
                'chNFSe' => $this->accessKey,
                'e'.$this->code => $this->detail,
            ],
        ];
    }
}
