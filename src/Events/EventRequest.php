<?php

namespace ChristianoErick\NfseNacional\Events;

use ChristianoErick\NfseNacional\Enums\AnalysisCancellationReason;
use ChristianoErick\NfseNacional\Enums\CancellationReason;
use ChristianoErick\NfseNacional\Enums\RejectionReason;
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

    /**
     * Solicitação de análise fiscal para cancelamento (e101103): quando o
     * município não deixa o emitente cancelar direto (ex.: E0823, valor acima
     * do permitido). A prefeitura defere (e105104) ou indefere (e105105).
     */
    public static function analysisCancellation(string $accessKey, AnalysisCancellationReason|string|int $reason, string $description): self
    {
        return new self($accessKey, '101103', [
            'xDesc' => 'Solicitação de Análise Fiscal para Cancelamento de NFS-e',
            'cMotivo' => $reason,
            'xMotivo' => $description,
        ]);
    }

    /** Manifestação: confirmação do prestador (e202201). */
    public static function providerConfirmation(string $accessKey): self
    {
        return new self($accessKey, '202201', ['xDesc' => 'Manifestação de NFS-e - Confirmação do Prestador']);
    }

    /** Manifestação: rejeição do prestador (e202205). */
    public static function providerRejection(string $accessKey, RejectionReason|string|int $reason, ?string $description = null): self
    {
        return new self($accessKey, '202205', array_filter([
            'xDesc' => 'Manifestação de NFS-e - Rejeição do Prestador',
            'cMotivo' => $reason,
            'xMotivo' => $description,
        ], fn ($value) => $value !== null && $value !== ''));
    }

    /** Manifestação: confirmação do tomador (e203202), para nota recebida. */
    public static function takerConfirmation(string $accessKey): self
    {
        return new self($accessKey, '203202', ['xDesc' => 'Manifestação de NFS-e - Confirmação do Tomador']);
    }

    /** Manifestação: rejeição do tomador (e203206), para nota recebida. */
    public static function takerRejection(string $accessKey, RejectionReason|string|int $reason, ?string $description = null): self
    {
        return new self($accessKey, '203206', array_filter([
            'xDesc' => 'Manifestação de NFS-e - Rejeição do Tomador',
            'cMotivo' => $reason,
            'xMotivo' => $description,
        ], fn ($value) => $value !== null && $value !== ''));
    }

    /**
     * Pelo código do catálogo (AuthorEvents): e101101, e101103, e202201...
     */
    public static function fromCode(string $accessKey, string $code, string|int|null $reason = null, ?string $description = null): self
    {
        return match ('e'.ltrim($code, 'e')) {
            'e101101' => self::cancellation($accessKey, (string) $reason, (string) $description),
            'e101103' => self::analysisCancellation($accessKey, (string) $reason, (string) $description),
            'e202201' => self::providerConfirmation($accessKey),
            'e202205' => self::providerRejection($accessKey, (string) $reason, $description),
            'e203202' => self::takerConfirmation($accessKey),
            'e203206' => self::takerRejection($accessKey, (string) $reason, $description),
            default => throw new InvalidDataException("Evento {$code} sem atalho: use EventRequest::make() com os campos do leiaute."),
        };
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
