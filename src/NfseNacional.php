<?php

namespace ChristianoErick\NfseNacional;

use ChristianoErick\NfseNacional\Certificate\Certificate;
use ChristianoErick\NfseNacional\Codes\ServiceCodes;
use ChristianoErick\NfseNacional\Danfse\DanfseRenderer;
use ChristianoErick\NfseNacional\Enums\Environment;
use ChristianoErick\NfseNacional\Http\NfseClient;
use ChristianoErick\NfseNacional\Http\SimulatedNfseClient;
use Illuminate\Http\Client\Factory as HttpFactory;
use Closure;
use SensitiveParameter;

/**
 * Ponto de entrada (facade NfseNacional).
 *
 *   $cert = NfseNacional::certificate(file_get_contents($pfx), $senha);
 *   $nota = NfseNacional::client($cert)->emit(Dps::make([...]));
 */
class NfseNacional
{
    /**
     * @param  array<string, mixed>|Closure(): array<string, mixed>  $config  array fixo ou leitura a cada uso (config('nfse-nacional'))
     */
    public function __construct(
        private readonly array|Closure $config,
        private readonly HttpFactory $http,
    ) {}

    /**
     * @param  bool|null  $simulate  true = SimulatedNfseClient (sem rede); null = config('nfse-nacional.simulate')
     */
    public function client(Certificate $certificate, Environment|string|int|null $environment = null, ?bool $simulate = null): NfseClient
    {
        $config = $this->config();
        $class = ($simulate ?? (bool) ($config['simulate'] ?? false)) ? SimulatedNfseClient::class : NfseClient::class;

        return new $class(
            $certificate,
            Environment::resolve($environment ?? ($config['environment'] ?? 'production')),
            $config,
            $this->http,
        );
    }

    public function certificate(#[SensitiveParameter] string $pfx, #[SensitiveParameter] string $password): Certificate
    {
        return Certificate::fromPfx($pfx, $password, $this->config()['openssl_binary'] ?? null);
    }

    public function certificateFromPath(string $path, #[SensitiveParameter] string $password): Certificate
    {
        return Certificate::fromPath($path, $password, $this->config()['openssl_binary'] ?? null);
    }

    public function danfse(): DanfseRenderer
    {
        return new DanfseRenderer;
    }

    public function services(): ServiceCodes
    {
        return new ServiceCodes;
    }

    /** @return array<string, mixed> */
    public function config(): array
    {
        return $this->config instanceof Closure ? ($this->config)() : $this->config;
    }
}
