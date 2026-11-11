<?php

namespace ChristianoErick\NfseNacional\Support;

use Stringable;

/**
 * Mensagem de processamento do Sistema Nacional (erro ou alerta), como vem
 * no JSON: codigo, descricao, complemento.
 */
final readonly class ProcessingMessage implements Stringable
{
    public function __construct(
        public ?string $code,
        public string $description,
        public ?string $complement = null,
    ) {}

    /**
     * Aceita as variações de caixa do JSON (codigo/Codigo, descricao/Descricao/mensagem).
     *
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $data = array_change_key_case($data, CASE_LOWER);

        return new self(
            code: isset($data['codigo']) ? (string) $data['codigo'] : null,
            description: trim((string) ($data['descricao'] ?? $data['mensagem'] ?? '')),
            complement: isset($data['complemento']) && $data['complemento'] !== '' ? (string) $data['complemento'] : null,
        );
    }

    /**
     * @param  mixed  $list  lista de mensagens, uma mensagem só, ou nada
     * @return list<self>
     */
    public static function listFrom(mixed $list): array
    {
        if (! is_array($list) || $list === []) {
            return [];
        }

        if (! array_is_list($list)) {
            $list = [$list];
        }

        return array_values(array_map(fn ($item) => is_array($item) ? self::fromArray($item) : new self(null, (string) $item), $list));
    }

    /** @return array{code: ?string, description: string, complement: ?string} */
    public function toArray(): array
    {
        return ['code' => $this->code, 'description' => $this->description, 'complement' => $this->complement];
    }

    public function __toString(): string
    {
        return trim(($this->code ? $this->code.' - ' : '').$this->description.($this->complement ? ' ('.$this->complement.')' : ''));
    }
}
