<?php

namespace ChristianoErick\NfseNacional;

use ChristianoErick\NfseNacional\Console\CertificateCommand;
use ChristianoErick\NfseNacional\Console\MunicipalityCommand;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Support\ServiceProvider;

class NfseNacionalServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/nfse-nacional.php', 'nfse-nacional');

        $this->app->singleton(NfseNacional::class, fn ($app) => new NfseNacional(
            (array) $app['config']->get('nfse-nacional', []),
            $app->make(HttpFactory::class),
        ));
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'nfse-nacional');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/nfse-nacional.php' => config_path('nfse-nacional.php'),
            ], 'nfse-nacional-config');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/nfse-nacional'),
            ], 'nfse-nacional-views');

            $this->commands([CertificateCommand::class, MunicipalityCommand::class]);
        }
    }
}
