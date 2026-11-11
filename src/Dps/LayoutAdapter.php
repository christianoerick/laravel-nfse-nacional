<?php

namespace ChristianoErick\NfseNacional\Dps;

use ChristianoErick\NfseNacional\Exceptions\InvalidDataException;
use ChristianoErick\NfseNacional\Support\Layout;

/**
 * Ajusta os campos do infDPS ao leiaute de destino, para o mesmo array
 * servir antes e depois da NT 009:
 *
 * - finNFSe: no infDPS (NT 009, obrigatório, padrão 0 = regular) ou em
 *   IBSCBS (1.01, só quando há IBSCBS).
 * - CST/cClassTrib do IBS/CBS: em IBSCBS/valores/trib (NT 009) ou em
 *   IBSCBS/valores/trib/gIBSCBS (1.01).
 *
 * Grupos sem equivalente direto (vDedRed × vAjusteBC, gReeRepRes,
 * bensMoveis) não são convertidos: geram erro com a orientação.
 */
final class LayoutAdapter
{
    /**
     * @param  array<string, mixed>  $infDps
     * @return array<string, mixed>
     */
    public static function adapt(array $infDps, Layout $layout): array
    {
        return $layout === Layout::Nt009 ? self::toNt009($infDps) : self::toV101($infDps);
    }

    /**
     * @param  array<string, mixed>  $d
     * @return array<string, mixed>
     */
    private static function toNt009(array $d): array
    {
        if (isset($d['valores']['vDedRed'])) {
            throw new InvalidDataException('valores/vDedRed não existe na NT 009: use valores/vAjusteBC (pAjusteBCISSQN, vAjusteBCISSQN ou documentos/docAjusteBC). Veja docs/CAMPOS.md.');
        }

        if (isset($d['IBSCBS']['valores']['gReeRepRes'])) {
            throw new InvalidDataException('IBSCBS/valores/gReeRepRes não existe na NT 009: os reembolsos/repasses vão em valores/vAjusteBC/documentos (tpAjusteBC 101 a 199).');
        }

        $finNFSe = $d['finNFSe'] ?? $d['IBSCBS']['finNFSe'] ?? '0';
        unset($d['IBSCBS']['finNFSe']);

        if (isset($d['IBSCBS']['valores']['trib']['gIBSCBS'])) {
            $group = $d['IBSCBS']['valores']['trib']['gIBSCBS'];

            foreach (['CST', 'cClassTrib'] as $field) {
                if (array_key_exists($field, $group)) {
                    $d['IBSCBS']['valores']['trib'][$field] ??= $group[$field];
                    unset($group[$field]);
                }
            }

            if ($group === []) {
                unset($d['IBSCBS']['valores']['trib']['gIBSCBS']);
            } else {
                $d['IBSCBS']['valores']['trib']['gIBSCBS'] = $group;
            }
        }

        return self::insertAfter($d, 'finNFSe', $finNFSe);
    }

    /**
     * @param  array<string, mixed>  $d
     * @return array<string, mixed>
     */
    private static function toV101(array $d): array
    {
        if (isset($d['valores']['vAjusteBC'])) {
            throw new InvalidDataException('valores/vAjusteBC é da NT 009: no leiaute 1.01 use valores/vDedRed. Veja docs/CAMPOS-v1.01.md.');
        }

        if (isset($d['IBSCBS']['bensMoveis'])) {
            throw new InvalidDataException('IBSCBS/bensMoveis é da NT 009 e não existe no leiaute 1.01.');
        }

        $finNFSe = $d['finNFSe'] ?? null;
        unset($d['finNFSe']);

        if (isset($d['IBSCBS'])) {
            $d['IBSCBS'] = ['finNFSe' => $d['IBSCBS']['finNFSe'] ?? $finNFSe ?? '0'] + $d['IBSCBS'];

            $trib = $d['IBSCBS']['valores']['trib'] ?? null;

            if (is_array($trib) && (isset($trib['CST']) || isset($trib['cClassTrib']))) {
                $group = $trib['gIBSCBS'] ?? [];

                foreach (['CST', 'cClassTrib'] as $field) {
                    if (array_key_exists($field, $trib)) {
                        $group[$field] ??= $trib[$field];
                        unset($trib[$field]);
                    }
                }

                $trib['gIBSCBS'] = $group;
                $d['IBSCBS']['valores']['trib'] = $trib;
            }
        }

        return $d;
    }

    /**
     * @param  array<string, mixed>  $d
     * @return array<string, mixed>
     */
    private static function insertAfter(array $d, string $key, mixed $value): array
    {
        // A ordem do array não importa (o XmlBuilder segue o XSD); só garante o campo.
        $d[$key] = $value;

        return $d;
    }
}
