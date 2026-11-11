<?php

namespace ChristianoErick\NfseNacional\Nfse;

use Carbon\CarbonImmutable;
use ChristianoErick\NfseNacional\Dps\Dps;
use ChristianoErick\NfseNacional\Exceptions\InvalidDataException;
use ChristianoErick\NfseNacional\Support\AccessKey;
use ChristianoErick\NfseNacional\Xml\XmlReader;
use ChristianoErick\NfseNacional\Xml\XmlSigner;

/**
 * NFS-e gerada (XML assinado pelo Sistema Nacional), com atalhos para o que
 * se costuma guardar e mostrar. data() devolve tudo, com os nomes oficiais.
 */
final class NfseDocument
{
    /** @var array<string, mixed> */
    private array $nfse;

    public function __construct(private readonly string $xml)
    {
        $data = XmlReader::toArray($xml);

        if (! isset($data['NFSe']['infNFSe'])) {
            throw new InvalidDataException('O XML não é uma NFS-e.');
        }

        $this->nfse = $data['NFSe']['infNFSe'];
    }

    public static function fromXml(string $xml): self
    {
        return new self($xml);
    }

    /** XML gzip+base64, como vem da API. */
    public static function fromGzipBase64(string $payload): self
    {
        $xml = gzdecode((string) base64_decode($payload, true));

        if ($xml === false) {
            throw new InvalidDataException('Conteúdo compactado da NFS-e inválido.');
        }

        return new self($xml);
    }

    public function xml(): string
    {
        return $this->xml;
    }

    /**
     * infNFSe completo (sem a assinatura), com os nomes oficiais.
     *
     * @return array<string, mixed>
     */
    public function data(): array
    {
        return $this->nfse;
    }

    public function get(string $path, mixed $default = null): mixed
    {
        $value = $this->nfse;

        foreach (explode('.', $path) as $segment) {
            if (! is_array($value) || ! array_key_exists($segment, $value)) {
                return $default;
            }

            $value = $value[$segment];
        }

        return $value;
    }

    public function accessKey(): string
    {
        return (string) preg_replace('/^NFS/', '', (string) $this->get('@Id'));
    }

    public function key(): AccessKey
    {
        return AccessKey::parse($this->accessKey());
    }

    public function number(): string
    {
        return (string) $this->get('nNFSe');
    }

    public function dpsId(): string
    {
        return (string) $this->get('DPS.infDPS.@Id');
    }

    public function dpsSeries(): string
    {
        return (string) $this->get('DPS.infDPS.serie');
    }

    public function dpsNumber(): string
    {
        return (string) $this->get('DPS.infDPS.nDPS');
    }

    public function status(): string
    {
        return (string) $this->get('cStat');
    }

    public function processedAt(): ?CarbonImmutable
    {
        $value = $this->get('dhProc');

        return $value ? CarbonImmutable::parse($value) : null;
    }

    public function issuedAt(): ?CarbonImmutable
    {
        $value = $this->get('DPS.infDPS.dhEmi');

        return $value ? CarbonImmutable::parse($value) : null;
    }

    public function competence(): ?CarbonImmutable
    {
        $value = $this->get('DPS.infDPS.dCompet');

        return $value ? CarbonImmutable::parse($value) : null;
    }

    /** Ambiente da DPS (1 produção, 2 produção restrita). */
    public function environment(): string
    {
        return (string) $this->get('DPS.infDPS.tpAmb');
    }

    /**
     * Prestador como gravado pelo Sistema Nacional (emit), com o regime da DPS.
     *
     * @return array<string, mixed>
     */
    public function provider(): array
    {
        return (array) $this->get('emit', []) + ['regTrib' => $this->get('DPS.infDPS.prest.regTrib', [])];
    }

    /** @return array<string, mixed> */
    public function taker(): array
    {
        return (array) $this->get('DPS.infDPS.toma', []);
    }

    /** @return array<string, mixed> */
    public function intermediary(): array
    {
        return (array) $this->get('DPS.infDPS.interm', []);
    }

    /** @return array<string, mixed> */
    public function service(): array
    {
        return (array) $this->get('DPS.infDPS.serv', []);
    }

    public function description(): string
    {
        return (string) $this->get('DPS.infDPS.serv.cServ.xDescServ');
    }

    public function serviceAmount(): string
    {
        return (string) $this->get('DPS.infDPS.valores.vServPrest.vServ', '0');
    }

    public function netAmount(): string
    {
        return (string) $this->get('valores.vLiq', $this->serviceAmount());
    }

    /** DPS que originou a nota, para duplicar ou substituir. */
    public function dps(): Dps
    {
        return Dps::fromXml($this->xml);
    }

    public function publicUrl(): string
    {
        return 'https://www.nfse.gov.br/ConsultaPublica?tpc=1&chave='.$this->accessKey();
    }

    /** Confere a assinatura do Sistema Nacional. */
    public function hasValidSignature(): bool
    {
        return XmlSigner::verify($this->xml);
    }

    /**
     * Resumo para guardar em banco.
     *
     * @return array<string, mixed>
     */
    public function summary(): array
    {
        return [
            'access_key' => $this->accessKey(),
            'number' => $this->number(),
            'dps_id' => $this->dpsId(),
            'dps_series' => $this->dpsSeries(),
            'dps_number' => $this->dpsNumber(),
            'status' => $this->status(),
            'environment' => $this->environment(),
            'processed_at' => $this->processedAt()?->toIso8601String(),
            'competence' => $this->competence()?->toDateString(),
            'provider_document' => $this->get('emit.CNPJ') ?? $this->get('emit.CPF'),
            'provider_name' => $this->get('emit.xNome'),
            'taker_document' => $this->get('DPS.infDPS.toma.CNPJ') ?? $this->get('DPS.infDPS.toma.CPF') ?? $this->get('DPS.infDPS.toma.NIF'),
            'taker_name' => $this->get('DPS.infDPS.toma.xNome'),
            'national_tax_code' => $this->get('DPS.infDPS.serv.cServ.cTribNac'),
            'description' => $this->description(),
            'service_amount' => $this->serviceAmount(),
            'net_amount' => $this->netAmount(),
        ];
    }
}
