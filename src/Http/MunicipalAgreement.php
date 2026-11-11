<?php

namespace ChristianoErick\NfseNacional\Http;

/**
 * Convênio do município com o Sistema Nacional (parametrizacao/{ibge}/convenio).
 */
final readonly class MunicipalAgreement
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function __construct(
        public string $cityIbgeCode,
        public array $parameters,
        public ?string $message = null,
    ) {}

    /** Município aderiu ao ambiente nacional (ADN). */
    public function joinedNationalEnvironment(): bool
    {
        return (int) ($this->parameters['aderenteAmbienteNacional'] ?? 0) === 1;
    }

    /** Contribuintes do município podem emitir pelos emissores públicos/API do Sefin Nacional. */
    public function usesNationalIssuer(): bool
    {
        return (int) ($this->parameters['aderenteEmissorNacional'] ?? 0) === 1;
    }

    public function joinedNationalAssessmentModule(): bool
    {
        return (int) ($this->parameters['aderenteMAN'] ?? 0) === 1;
    }
}
