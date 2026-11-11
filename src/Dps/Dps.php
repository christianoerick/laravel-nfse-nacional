<?php

namespace ChristianoErick\NfseNacional\Dps;

use ChristianoErick\NfseNacional\Enums\Environment;
use ChristianoErick\NfseNacional\Exceptions\InvalidDataException;
use ChristianoErick\NfseNacional\Support\Document;
use ChristianoErick\NfseNacional\Support\DpsId;
use ChristianoErick\NfseNacional\Support\Layout;
use ChristianoErick\NfseNacional\Xml\XmlBuilder;
use ChristianoErick\NfseNacional\Xml\XmlReader;
use DateTimeInterface;

/**
 * DPS completa, com os nomes oficiais das tags do grupo infDPS (veja
 * docs/CAMPOS.md). O Id é calculado (cLocEmi + documento do emitente +
 * série + número); tpAmb, verAplic e dhEmi são preenchidos pelo cliente na
 * emissão quando não vierem.
 *
 *   Dps::make([
 *       'serie' => 1, 'nDPS' => 15, 'dCompet' => now(), 'tpEmit' => 1,
 *       'cLocEmi' => '2704302',
 *       'prest' => ['CNPJ' => '...', 'regTrib' => ['opSimpNac' => 3, 'regApTribSN' => 1, 'regEspTrib' => 0]],
 *       'toma' => ['CNPJ' => '...', 'xNome' => '...'],
 *       'serv' => ['locPrest' => ['cLocPrestacao' => '2704302'], 'cServ' => ['cTribNac' => '010501', 'xDescServ' => '...']],
 *       'valores' => ['vServPrest' => ['vServ' => 7500], 'trib' => [...]],
 *   ]);
 */
final class Dps
{
    public const VERSION = '1.01';

    /**
     * @param  array<string, mixed>  $infDps
     */
    private function __construct(private array $infDps, private string $version = self::VERSION) {}

    /**
     * @param  array<string, mixed>  $infDps  campos de infDPS (sem @Id)
     */
    public static function make(array $infDps, string $version = self::VERSION): self
    {
        return new self($infDps, $version);
    }

    /**
     * DPS de um XML (DPS avulsa ou a que vem dentro de uma NFS-e), sem a
     * assinatura. Base para duplicar uma nota.
     */
    public static function fromXml(string $xml): self
    {
        $data = XmlReader::toArray($xml);
        $dps = $data['DPS'] ?? $data['NFSe']['infNFSe']['DPS'] ?? null;

        if (! is_array($dps) || ! isset($dps['infDPS'])) {
            throw new InvalidDataException('O XML não contém uma DPS.');
        }

        $infDps = $dps['infDPS'];
        unset($infDps['@Id']);

        return new self($infDps, (string) ($dps['@versao'] ?? self::VERSION));
    }

    /**
     * @return array<string, mixed>
     */
    public function fields(): array
    {
        return $this->infDps;
    }

    public function get(string $path, mixed $default = null): mixed
    {
        $value = $this->infDps;

        foreach (explode('.', $path) as $segment) {
            if (! is_array($value) || ! array_key_exists($segment, $value)) {
                return $default;
            }

            $value = $value[$segment];
        }

        return $value;
    }

    /** Cópia com um campo trocado (caminho com ponto: "valores.vServPrest.vServ"). */
    public function with(string $path, mixed $value): self
    {
        $copy = clone $this;
        $target = &$copy->infDps;

        foreach (explode('.', $path) as $segment) {
            if (! isset($target[$segment]) || ! is_array($target[$segment])) {
                $target[$segment] ??= [];
            }

            $target = &$target[$segment];
        }

        $target = $value;
        unset($target);

        return $copy;
    }

    /**
     * Base para uma nova nota a partir desta (duplicar): tira data/hora de
     * emissão, versão do aplicativo, ambiente e substituição, e troca série
     * e número. O resto (prestador, tomador, serviço, tributos) continua.
     */
    public function duplicate(string|int $series, string|int $number): self
    {
        return $this->without('dhEmi')->without('verAplic')->without('tpAmb')->without('subst')
            ->with('serie', $series)
            ->with('nDPS', $number);
    }

    /** Cópia sem um campo. */
    public function without(string $path): self
    {
        $copy = clone $this;
        $segments = explode('.', $path);
        $last = array_pop($segments);
        $target = &$copy->infDps;

        foreach ($segments as $segment) {
            if (! isset($target[$segment]) || ! is_array($target[$segment])) {
                return $copy;
            }

            $target = &$target[$segment];
        }

        unset($target[$last]);

        return $copy;
    }

    /**
     * Completa o que o emissor sabe: ambiente, versão do aplicativo e data/hora
     * de emissão (um minuto atrás: o Sefin recusa dhEmi no futuro, e os
     * relógios divergem).
     */
    public function withDefaults(Environment $environment, string $applicationVersion, ?DateTimeInterface $now = null): self
    {
        $copy = clone $this;
        $copy->infDps['tpAmb'] ??= $environment;
        $copy->infDps['verAplic'] ??= $applicationVersion;
        $copy->infDps['tpEmit'] ??= 1;
        $copy->infDps['dhEmi'] ??= \Carbon\CarbonImmutable::instance($now ?? new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')))->setTimezone('America/Sao_Paulo')->subMinute();

        return $copy;
    }

    /** Id da DPS (45 posições). */
    public function id(): string
    {
        $issuer = match ((string) $this->scalar($this->infDps['tpEmit'] ?? 1)) {
            '2' => $this->infDps['toma'] ?? [],
            '3' => $this->infDps['interm'] ?? [],
            default => $this->infDps['prest'] ?? [],
        };

        $document = $issuer['CNPJ'] ?? $issuer['CPF'] ?? null;

        if ($document === null) {
            throw new InvalidDataException('Informe o CNPJ ou CPF do emitente da DPS.');
        }

        return DpsId::make(
            (string) $this->scalar($this->infDps['cLocEmi'] ?? ''),
            Document::clean((string) $document),
            (string) $this->scalar($this->infDps['serie'] ?? ''),
            (string) $this->scalar($this->infDps['nDPS'] ?? ''),
        );
    }

    /**
     * Estrutura completa para o XmlBuilder (raiz DPS).
     *
     * @return array<string, mixed>
     */
    public function toArray(?Layout $layout = null): array
    {
        $infDps = LayoutAdapter::adapt($this->infDps, $layout ?? Layout::current());
        $infDps['serie'] = DpsId::series((string) $this->scalar($infDps['serie'] ?? ''));
        $infDps['nDPS'] = (string) $this->scalar($infDps['nDPS'] ?? '');

        return ['@versao' => $this->version, 'infDPS' => ['@Id' => $this->id()] + $infDps];
    }

    /** XML sem assinatura, no leiaute informado (padrão: o de produção hoje). */
    public function toXml(?Layout $layout = null): string
    {
        $layout ??= Layout::current();

        return (new XmlBuilder($layout))->toXml('DPS', $this->toArray($layout));
    }

    private function scalar(mixed $value): mixed
    {
        return $value instanceof \BackedEnum ? $value->value : $value;
    }
}
