<?php

namespace ChristianoErick\NfseNacional\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \ChristianoErick\NfseNacional\Http\NfseClient client(\ChristianoErick\NfseNacional\Certificate\Certificate $certificate, \ChristianoErick\NfseNacional\Enums\Environment|string|int|null $environment = null)
 * @method static \ChristianoErick\NfseNacional\Certificate\Certificate certificate(string $pfx, string $password)
 * @method static \ChristianoErick\NfseNacional\Certificate\Certificate certificateFromPath(string $path, string $password)
 * @method static \ChristianoErick\NfseNacional\Danfse\DanfseRenderer danfse()
 * @method static \ChristianoErick\NfseNacional\Codes\ServiceCodes services()
 *
 * @see \ChristianoErick\NfseNacional\NfseNacional
 */
class NfseNacional extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \ChristianoErick\NfseNacional\NfseNacional::class;
    }
}
