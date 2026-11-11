<?php

namespace ChristianoErick\NfseNacional\Danfse;

use ChristianoErick\NfseNacional\Codes\ServiceCodes;
use ChristianoErick\NfseNacional\Enums\FederalWithholding;
use ChristianoErick\NfseNacional\Enums\IssqnTaxation;
use ChristianoErick\NfseNacional\Enums\IssqnWithholding;
use ChristianoErick\NfseNacional\Enums\IssuerRole;
use ChristianoErick\NfseNacional\Enums\NfseStatus;
use ChristianoErick\NfseNacional\Enums\SimplesNacionalOption;
use ChristianoErick\NfseNacional\Enums\SimplesNacionalRegime;
use ChristianoErick\NfseNacional\Nfse\NfseDocument;
use ChristianoErick\NfseNacional\Support\Document;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Output\QRMarkupSVG;
use chillerlan\QRCode\QROptions;
use Closure;
use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * DANFSe v2.0 (Documento Auxiliar da NFS-e) no leiaute do Sistema
 * Nacional, gerado localmente a partir do XML da nota. O PDF oficial também
 * pode ser baixado pelo NfseClient::danfse().
 *
 * O XML só traz o nome do município do emitente, da prestação e da
 * incidência; para os demais (tomador, intermediário) informe um resolvedor
 * com resolveCityUsing(fn (string $ibge) => 'Nome').
 */
class DanfseRenderer
{
    private const STATES = [
        '11' => 'RO', '12' => 'AC', '13' => 'AM', '14' => 'RR', '15' => 'PA', '16' => 'AP', '17' => 'TO',
        '21' => 'MA', '22' => 'PI', '23' => 'CE', '24' => 'RN', '25' => 'PB', '26' => 'PE', '27' => 'AL', '28' => 'SE', '29' => 'BA',
        '31' => 'MG', '32' => 'ES', '33' => 'RJ', '35' => 'SP', '41' => 'PR', '42' => 'SC', '43' => 'RS',
        '50' => 'MS', '51' => 'MT', '52' => 'GO', '53' => 'DF',
    ];

    private ?Closure $cityResolver = null;

    private ?string $watermark = null;

    /** @param  Closure(string): ?string  $resolver */
    public function resolveCityUsing(Closure $resolver): static
    {
        $this->cityResolver = $resolver;

        return $this;
    }

    /** Marca d'água (ex.: "CANCELADA", "SEM VALOR FISCAL"). */
    public function watermark(?string $text): static
    {
        $this->watermark = $text;

        return $this;
    }

    /** PDF (bytes). */
    public function render(NfseDocument $nfse): string
    {
        $options = new Options;
        $options->setIsRemoteEnabled(false);
        $options->setDefaultFont('DejaVu Sans');
        $options->setChroot(dirname(__DIR__, 2).'/resources');

        $dompdf = new Dompdf($options);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->loadHtml($this->html($nfse), 'UTF-8');
        $dompdf->render();

        return (string) $dompdf->output();
    }

    public function html(NfseDocument $nfse): string
    {
        return view('nfse-nacional::danfse', ['d' => $this->data($nfse)])->render();
    }

    /**
     * Tudo que o DANFSe mostra, já formatado ("-" quando vazio).
     *
     * @return array<string, mixed>
     */
    public function data(NfseDocument $nfse): array
    {
        $g = fn (string $path) => $nfse->get($path);
        $dps = 'DPS.infDPS.';
        $prest = $dps.'prest.';
        $toma = $dps.'toma.';
        $interm = $dps.'interm.';
        $trib = $dps.'valores.trib.';
        $ibs = 'IBSCBS.';

        $emitCity = (string) $g('emit.enderNac.cMun');
        $cityNames = array_filter([
            $emitCity => $g('xLocEmi'),
            (string) $g($dps.'serv.locPrest.cLocPrestacao') => $g('xLocPrestacao'),
            (string) $g('cLocIncid') => $g('xLocIncid'),
        ]);

        $city = function (?string $ibge) use ($cityNames): string {
            if (! $ibge) {
                return '-';
            }

            $name = $cityNames[$ibge] ?? ($this->cityResolver ? ($this->cityResolver)($ibge) : null) ?? $ibge;

            return $name.' / '.(self::STATES[substr($ibge, 0, 2)] ?? '-');
        };

        $party = function (string $base, bool $isProvider = false) use ($g, $city): ?array {
            $source = $isProvider ? 'emit.' : $base;
            $document = $g($source.'CNPJ') ?? $g($source.'CPF') ?? $g($base.'CNPJ') ?? $g($base.'CPF') ?? $g($base.'NIF');

            if ($document === null && $g($base.'xNome') === null) {
                return null;
            }

            $address = $isProvider ? 'emit.enderNac.' : $base.'end.';
            $cityCode = $isProvider ? $g('emit.enderNac.cMun') : $g($base.'end.endNac.cMun');
            $zip = $isProvider ? $g('emit.enderNac.CEP') : $g($base.'end.endNac.CEP');
            $foreign = $g($base.'end.endExt.cPais');

            return [
                'document' => $document ? Document::format((string) $document) : '-',
                'municipal_registration' => $g($base.'IM') ?? ($isProvider ? $g('emit.IM') : null) ?? '-',
                'phone' => self::phone($g($base.'fone') ?? ($isProvider ? $g('emit.fone') : null)),
                'name' => $g($isProvider ? 'emit.xNome' : $base.'xNome') ?? '-',
                'city' => $foreign ? ($g($base.'end.endExt.xCidade').' / '.$foreign) : $city($cityCode),
                'ibge_zip' => $cityCode ? substr($cityCode, 0, 2).'.'.substr($cityCode, 2).' / '.self::zip($zip) : ($foreign ? $g($base.'end.endExt.cEndPost') ?? '-' : '-'),
                'address' => self::address([$g($address.'xLgr'), $g($address.'nro'), $g($address.'xCpl'), $g($address.'xBairro')]),
                'email' => $g($base.'email') ?? ($isProvider ? $g('emit.email') : null) ?? '-',
            ];
        };

        $label = fn (string $enum, mixed $value) => $value === null || $value === '' ? '-' : ($enum::tryFrom((string) $value)?->label() ?? (string) $value);

        $nationalCode = (string) $g($dps.'serv.cServ.cTribNac');
        $immunity = $g($trib.'tribMun.tribISSQN');
        $pisCofins = $g($trib.'tribFed.piscofins');
        $totalRetained = $g('valores.vTotalRet');

        return [
            'watermark' => $this->watermark,
            'logo' => 'data:image/png;base64,'.base64_encode((string) file_get_contents(dirname(__DIR__, 2).'/resources/images/logo-nfse.png')),
            'city' => $g('xLocEmi') ? $g('xLocEmi').' - '.(self::STATES[substr($emitCity, 0, 2)] ?? '') : '-',
            'generator_environment' => $g('ambGer') ?? '-',
            'environment' => $g($dps.'tpAmb') ?? '-',
            'access_key' => $nfse->accessKey(),
            'qr_code' => $this->qrCode($nfse->publicUrl()),
            'number' => $nfse->number() ?: '-',
            'competence' => self::date($g($dps.'dCompet')),
            'processed_at' => self::dateTime($g('dhProc')),
            'dps_number' => $g($dps.'nDPS') ?? '-',
            // Como no oficial: sem os zeros à esquerda (00001 → 1).
            'dps_series' => $g($dps.'serie') !== null ? (ltrim((string) $g($dps.'serie'), '0') ?: '0') : '-',
            'dps_issued_at' => self::dateTime($g($dps.'dhEmi')),
            'issuer_role' => $label(IssuerRole::class, $g($dps.'tpEmit') ?? '1'),
            'status' => $g('cStat') === '100' || $g('cStat') === null ? 'NFS-e Gerada' : $label(NfseStatus::class, $g('cStat')),
            'purpose' => $g($dps.'subst.chSubstda') ? 'Substituição' : '-',
            'provider' => $party($prest, true),
            'simples' => $label(SimplesNacionalOption::class, $g($prest.'regTrib.opSimpNac')),
            'simples_regime' => str_replace(' SN', ' Simples Nacional', $label(SimplesNacionalRegime::class, $g($prest.'regTrib.regApTribSN'))),
            'taker' => $party($toma),
            'recipient' => $g($dps.'IBSCBS.dest.xNome') ? $party($dps.'IBSCBS.dest.') : null,
            'intermediary' => $party($interm),
            'service_code' => ($nationalCode ? ServiceCodes::format($nationalCode) : '-').' / '.($g($dps.'serv.cServ.cTribMun') ?? '-'),
            'nbs' => $g($dps.'serv.cServ.cNBS') ?? '-',
            'service_place' => ($g('xLocPrestacao') ?? $city($g($dps.'serv.locPrest.cLocPrestacao'))).' / '.(self::STATES[substr((string) $g($dps.'serv.locPrest.cLocPrestacao'), 0, 2)] ?? '-').' / '.($g($dps.'serv.locPrest.cPaisPrestacao') ?? '-'),
            // O oficial mostra a descrição municipal quando existe.
            'national_tax_description' => $g('xTribMun') ? trim((string) $g('xTribMun')) : ($g('xTribNac') ?? '-'),
            'description' => $g($dps.'serv.cServ.xDescServ') ?? '-',
            'issqn_taxation' => $label(IssqnTaxation::class, $immunity),
            'issqn_place' => ($g('xLocIncid') ?? '-').' / '.(self::STATES[substr((string) $g('cLocIncid'), 0, 2)] ?? '-').' / '.($g($trib.'tribMun.cPaisResult') ?? '-'),
            'issqn_base' => self::money($g('valores.vBC')),
            'issqn_rate' => self::percent($g('valores.pAliqAplic')),
            'issqn_withholding' => $label(IssqnWithholding::class, $g($trib.'tribMun.tpRetISSQN')),
            'issqn_amount' => self::money($g('valores.vISSQN')),
            'irrf' => self::money($g($trib.'tribFed.vRetIRRF')),
            'cp' => self::money($g($trib.'tribFed.vRetCP')),
            'csll' => self::money($g($trib.'tribFed.vRetCSLL')),
            'pis' => self::money(is_array($pisCofins) ? ($pisCofins['vPis'] ?? null) : null),
            'cofins' => self::money(is_array($pisCofins) ? ($pisCofins['vCofins'] ?? null) : null),
            'federal_withholding' => is_array($pisCofins) && isset($pisCofins['tpRetPisCofins']) ? $pisCofins['tpRetPisCofins'].' - '.$label(FederalWithholding::class, $pisCofins['tpRetPisCofins']) : '-',
            'ibs' => [
                'cst' => ($g($dps.'IBSCBS.valores.trib.gIBSCBS.CST') ?? '-').' / '.($g($dps.'IBSCBS.valores.trib.gIBSCBS.cClassTrib') ?? '-'),
                'operation' => implode(' / ', [$g($dps.'IBSCBS.cIndOp') ?? '-', $g($ibs.'cLocalidadeIncid') ?? '-', $g($ibs.'xLocalidadeIncid') ?? '-', self::STATES[substr((string) $g($ibs.'cLocalidadeIncid'), 0, 2)] ?? '-']),
                'reductions' => self::money($g($ibs.'valores.vCalcReeRepRes') ?? '0', zero: true),
                'base' => self::money($g($ibs.'valores.vBC')),
                'rate_reduction' => implode(' / ', [self::percent($g($ibs.'valores.uf.pRedAliqUF')), self::percent($g($ibs.'valores.mun.pRedAliqMun')), self::percent($g($ibs.'valores.fed.pRedAliqCBS'))]),
                'rates' => self::percent($g($ibs.'valores.uf.pIBSUF')).' / '.self::percent($g($ibs.'valores.mun.pIBSMun')),
                'effective_mun' => self::percent($g($ibs.'valores.mun.pAliqEfetMun')),
                'amount_mun' => self::money($g($ibs.'totCIBS.gIBS.gIBSMunTot.vIBSMun')),
                'effective_uf' => self::percent($g($ibs.'valores.uf.pAliqEfetUF')),
                'amount_uf' => self::money($g($ibs.'totCIBS.gIBS.gIBSUFTot.vIBSUF')),
                'total_ibs' => self::money($g($ibs.'totCIBS.gIBS.vIBSTot')),
                'cbs_rate' => self::percent($g($ibs.'valores.fed.pCBS')),
                'cbs_effective' => self::percent($g($ibs.'valores.fed.pAliqEfetCBS')),
                'total_cbs' => self::money($g($ibs.'totCIBS.gCBS.vCBS')),
            ],
            'service_amount' => self::money($g($dps.'valores.vServPrest.vServ')),
            'unconditional_discount' => self::money($g($dps.'valores.vDescCondIncond.vDescIncond')),
            'conditional_discount' => self::money($g($dps.'valores.vDescCondIncond.vDescCond')),
            'total_retained' => $totalRetained === null ? '-' : self::money($totalRetained, zero: true),
            'net_amount' => self::money($g('valores.vLiq')),
            'total_ibs_cbs' => self::money((float) ($g($ibs.'totCIBS.gIBS.vIBSTot') ?? 0) + (float) ($g($ibs.'totCIBS.gCBS.vCBS') ?? 0), zero: true),
            'net_plus_ibs_cbs' => self::money($g($ibs.'totCIBS.vTotNF') ?? '0', zero: true),
            'additional_info' => $this->additionalInfo($nfse),
        ];
    }

    private function additionalInfo(NfseDocument $nfse): string
    {
        $dps = 'DPS.infDPS.';
        $parts = array_filter([
            $nfse->get('xOutInf'),
            $nfse->get($dps.'serv.infoCompl.xInfComp'),
            $nfse->get($dps.'serv.infoCompl.idDocTec') ? 'Doc. responsabilidade técnica: '.$nfse->get($dps.'serv.infoCompl.idDocTec') : null,
            $nfse->get($dps.'serv.infoCompl.docRef') ? 'Documento de referência: '.$nfse->get($dps.'serv.infoCompl.docRef') : null,
            $nfse->get($dps.'serv.infoCompl.xPed') ? 'Pedido: '.$nfse->get($dps.'serv.infoCompl.xPed') : null,
        ]);

        $total = $nfse->get($dps.'valores.trib.totTrib');
        $line = 'Totais aproximados dos Tributos cfe. Lei n° 12.741/2012: ';

        if (isset($total['pTotTrib'])) {
            $parts[] = $line.'Federais: '.self::percent($total['pTotTrib']['pTotTribFed'] ?? null).'; Estaduais: '.self::percent($total['pTotTrib']['pTotTribEst'] ?? null).'; Municipais: '.self::percent($total['pTotTrib']['pTotTribMun'] ?? null).';';
        } elseif (isset($total['vTotTrib'])) {
            $parts[] = $line.'Federais: '.self::money($total['vTotTrib']['vTotTribFed'] ?? null).'; Estaduais: '.self::money($total['vTotTrib']['vTotTribEst'] ?? null).'; Municipais: '.self::money($total['vTotTrib']['vTotTribMun'] ?? null).';';
        } else {
            // Inclusive com pTotTribSN (Simples Nacional): o oficial mostra os traços.
            $parts[] = $line.'Federais: -; Estaduais: -; Municipais: -;';
        }

        return implode("\n", $parts);
    }

    private function qrCode(string $url): string
    {
        // Compatível com chillerlan/php-qrcode 5 e 6.
        $options = new QROptions;
        $options->outputInterface = QRMarkupSVG::class;
        $options->outputBase64 = true;
        $options->eccLevel = EccLevel::M;
        $options->addQuietzone = false;

        return (new QRCode($options))->render($url);
    }

    private static function money(mixed $value, bool $zero = false): string
    {
        if ($value === null || $value === '') {
            return $zero ? 'R$ 0,00' : '-';
        }

        return 'R$ '.number_format((float) $value, 2, ',', '.');
    }

    private static function percent(mixed $value): string
    {
        return $value === null || $value === '' ? '-' : number_format((float) $value, 2, ',', '.').' %';
    }

    private static function date(?string $value): string
    {
        return $value ? date('d/m/Y', strtotime($value)) : '-';
    }

    private static function dateTime(?string $value): string
    {
        if (! $value) {
            return '-';
        }

        // Mostra a hora como veio (fuso do emitente), sem converter.
        return preg_match('/^(\d{4})-(\d{2})-(\d{2})T(\d{2}:\d{2}:\d{2})/', $value, $m) ? "{$m[3]}/{$m[2]}/{$m[1]} {$m[4]}" : $value;
    }

    private static function phone(?string $value): string
    {
        $digits = preg_replace('/\D/', '', (string) $value) ?? '';

        return match (strlen($digits)) {
            10 => '('.substr($digits, 0, 2).') '.substr($digits, 2, 4).'-'.substr($digits, 6),
            11 => '('.substr($digits, 0, 2).') '.substr($digits, 2, 5).'-'.substr($digits, 7),
            0 => '-',
            default => (string) $value,
        };
    }

    private static function zip(?string $value): string
    {
        $digits = preg_replace('/\D/', '', (string) $value) ?? '';

        return strlen($digits) === 8 ? substr($digits, 0, 2).'.'.substr($digits, 2, 3).'-'.substr($digits, 5) : ($value ?: '-');
    }

    /** @param  list<mixed>  $parts */
    private static function address(array $parts): string
    {
        $parts = array_values(array_filter(array_map(fn ($part) => is_string($part) ? trim((string) preg_replace('/\s+/', ' ', $part)) : null, $parts)));

        return $parts === [] ? '-' : implode(', ', $parts);
    }
}
