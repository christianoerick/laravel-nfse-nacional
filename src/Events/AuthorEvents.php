<?php

namespace ChristianoErick\NfseNacional\Events;

use ChristianoErick\NfseNacional\Enums\AnalysisCancellationReason;
use ChristianoErick\NfseNacional\Enums\CancellationReason;
use ChristianoErick\NfseNacional\Enums\RejectionReason;

/**
 * Eventos que o contribuinte registra numa NFS-e, com os motivos de cada um,
 * para montar a tela ("Registrar evento"). Os de análise fiscal (e105104,
 * e105105) e de ofício (e305101) são do município, não entram aqui.
 */
final class AuthorEvents
{
    /**
     * @param  'provider'|'taker'|null  $role  quem registra: prestador (nota emitida) ou tomador (nota recebida)
     * @return list<array{code: string, label: string, role: string, reasons: list<array{value: string, label: string}>, description: 'required'|'optional'|'none', min: int, effect: string}>
     */
    public static function all(?string $role = null): array
    {
        $events = [
            [
                'code' => 'e101101',
                'label' => 'Cancelamento de NFS-e',
                'role' => 'provider',
                'reasons' => CancellationReason::options(),
                'description' => 'required',
                'min' => 15,
                'effect' => 'Cancela na hora. O município pode limitar por valor ou prazo (E0823): nesse caso, use a análise fiscal.',
            ],
            [
                'code' => 'e101103',
                'label' => 'Solicitação de análise fiscal para cancelamento',
                'role' => 'provider',
                'reasons' => AnalysisCancellationReason::options(),
                'description' => 'required',
                'min' => 15,
                'effect' => 'Pede ao município para cancelar. A nota segue válida até o deferimento (e105104); se indeferir (e105105), continua emitida.',
            ],
            [
                'code' => 'e202201',
                'label' => 'Manifestação: confirmação do prestador',
                'role' => 'provider',
                'reasons' => [],
                'description' => 'none',
                'min' => 0,
                'effect' => 'Confirma que o serviço da nota foi prestado.',
            ],
            [
                'code' => 'e202205',
                'label' => 'Manifestação: rejeição do prestador',
                'role' => 'provider',
                'reasons' => RejectionReason::options(),
                'description' => 'optional',
                'min' => 15,
                'effect' => 'Rejeita a nota (ex.: emitida pelo tomador/intermediário em nome do prestador).',
            ],
            [
                'code' => 'e203202',
                'label' => 'Manifestação: confirmação do tomador',
                'role' => 'taker',
                'reasons' => [],
                'description' => 'none',
                'min' => 0,
                'effect' => 'Confirma, como tomador, que recebeu o serviço.',
            ],
            [
                'code' => 'e203206',
                'label' => 'Manifestação: rejeição do tomador',
                'role' => 'taker',
                'reasons' => RejectionReason::options(),
                'description' => 'optional',
                'min' => 15,
                'effect' => 'Rejeita, como tomador, a nota recebida.',
            ],
        ];

        return array_values(array_filter($events, fn (array $event) => $role === null || $event['role'] === $role));
    }

    /**
     * @return array{code: string, label: string, role: string, reasons: list<array{value: string, label: string}>, description: string, min: int, effect: string}|null
     */
    public static function find(string $code): ?array
    {
        $code = 'e'.ltrim($code, 'e');

        foreach (self::all() as $event) {
            if ($event['code'] === $code) {
                return $event;
            }
        }

        return null;
    }
}
