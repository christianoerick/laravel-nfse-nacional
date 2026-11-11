<?php

namespace ChristianoErick\NfseNacional\Http;

use Carbon\CarbonImmutable;
use ChristianoErick\NfseNacional\Dps\Dps;
use ChristianoErick\NfseNacional\Events\EventRequest;
use ChristianoErick\NfseNacional\Exceptions\NotFoundException;
use ChristianoErick\NfseNacional\Nfse\NfseDocument;
use ChristianoErick\NfseNacional\Support\Document;
use ChristianoErick\NfseNacional\Support\ProcessingMessage;
use ChristianoErick\NfseNacional\Xml\SchemaMap;
use ChristianoErick\NfseNacional\Xml\XmlBuilder;
use ChristianoErick\NfseNacional\Xml\XmlReader;
use ChristianoErick\NfseNacional\Xml\XmlSigner;

/**
 * Simulação sem rede: monta, valida no XSD e assina a DPS como o cliente
 * real, mas devolve uma NFS-e gerada aqui (assinada com o próprio
 * certificado, número 9xxxxxxx, xOutInf "SIMULAÇÃO") e aceita os eventos.
 * Nada vai para o Sistema Nacional. Serve para testar o fluxo inteiro de um
 * sistema (telas, e-mail, DANFSe, eventos) sem produção nem convênio na
 * produção restrita.
 *
 * NfseNacional::client($certificado, 'restricted', simulate: true)
 */
class SimulatedNfseClient extends NfseClient
{
    /** @var array<string, string> chave => XML da NFS-e simulada (vida do processo) */
    private static array $issued = [];

    /** @var array<string, string> Id da DPS => chave */
    private static array $byDps = [];

    public const NOTICE = 'SIMULAÇÃO - documento gerado sem envio ao Sistema Nacional NFS-e, sem valor fiscal.';

    public function isSimulated(): bool
    {
        return true;
    }

    public function emit(Dps $dps): EmissionResult
    {
        $prepared = $this->prepare($dps);
        $signed = $this->signDps($prepared);
        $nfse = NfseDocument::fromXml($this->buildNfse($signed, $prepared));

        self::$issued[$nfse->accessKey()] = $nfse->xml();
        self::$byDps[$prepared->id()] = $nfse->accessKey();

        return new EmissionResult(
            nfse: $nfse,
            signedDpsXml: $signed,
            alerts: [new ProcessingMessage('SIM', self::NOTICE)],
            response: ['simulated' => true],
        );
    }

    public function emitOrRecover(Dps $dps): EmissionResult
    {
        $existing = $this->findByDps($this->prepare($dps)->id());

        return $existing ? new EmissionResult($existing, signedDpsXml: '', recovered: true) : $this->emit($dps);
    }

    public function dpsExists(string $dpsId): bool
    {
        return isset(self::$byDps[$dpsId]);
    }

    public function accessKeyForDps(string $dpsId): ?string
    {
        return self::$byDps[$dpsId] ?? null;
    }

    public function find(string $accessKey): ?NfseDocument
    {
        return isset(self::$issued[$accessKey]) ? NfseDocument::fromXml(self::$issued[$accessKey]) : null;
    }

    public function registerEvent(EventRequest $event): EventResult
    {
        $signed = $this->signEvent($event);
        $now = CarbonImmutable::now('America/Sao_Paulo');
        $request = XmlReader::toArray($signed)['pedRegEvento']['infPedReg'] ?? [];
        $detail = $request['e'.$event->code] ?? [];

        $xml = '<?xml version="1.0" encoding="UTF-8"?><evento xmlns="'.SchemaMap::NAMESPACE.'" versao="1.01"><infEvento Id="EVT'.$event->accessKey.$event->code.'001">'
            .'<verAplic>Simulacao</verAplic><ambGer>2</ambGer><nSeqEvento>1</nSeqEvento><dhProc>'.$now->format('Y-m-d\TH:i:sP').'</dhProc>'
            .'<nDFe>0</nDFe><e'.$event->code.'><xDesc>'.htmlspecialchars((string) ($detail['xDesc'] ?? 'Evento simulado')).'</xDesc></e'.$event->code.'>'
            .'<xObs>'.self::NOTICE.'</xObs></infEvento></evento>';

        return new EventResult($xml, $signed, ['dataHoraProcessamento' => $now->format('Y-m-d\TH:i:sP'), 'simulated' => true]);
    }

    public function event(string $accessKey, string $type, int $sequence = 1): ?EventResult
    {
        return null;
    }

    public function events(string $accessKey): array
    {
        return [];
    }

    public function danfse(string $accessKey): string
    {
        throw new NotFoundException('Simulação: não há DANFSe oficial (use o gerado pelo pacote).');
    }

    public function distribution(int $nsu, bool $lote = true): array
    {
        return ['LoteDFe' => [], 'simulated' => true];
    }

    public function municipalAgreement(string $cityIbgeCode): MunicipalAgreement
    {
        return new MunicipalAgreement($cityIbgeCode, ['aderenteAmbienteNacional' => 1, 'aderenteEmissorNacional' => 1, 'simulated' => true], 'Simulação.');
    }

    public function municipalRate(string $cityIbgeCode, string $serviceCode, string $competence, ?string $municipalCode = null): array
    {
        $code = \ChristianoErick\NfseNacional\Codes\ServiceCodes::parameterCode($serviceCode, $municipalCode);

        return ['aliquotas' => [$code => [['Incidencia' => 'SIM', 'Aliq' => 0, 'DtIni' => null, 'DtFim' => null]]], 'mensagem' => 'Simulação.'];
    }

    /**
     * NFS-e simulada no leiaute em uso, com a DPS assinada dentro e assinada
     * (SHA-256) com o próprio certificado.
     */
    private function buildNfse(string $signedDps, Dps $dps): string
    {
        $fields = $dps->fields();
        $prest = $fields['prest'] ?? [];
        $document = Document::clean((string) ($prest['CNPJ'] ?? $prest['CPF'] ?? $this->certificate()->document()));
        $city = (string) ($fields['cLocEmi'] ?? '0000000');
        $now = CarbonImmutable::now('America/Sao_Paulo');
        $number = (string) random_int(90_000_000, 99_999_999);
        $key = self::accessKey($city, $document, $number, $now);
        $amount = (string) ($fields['valores']['vServPrest']['vServ'] ?? '0');

        $nfse = (new XmlBuilder($this->layout()))->build('NFSe', [
            '@versao' => '1.01',
            'infNFSe' => [
                '@Id' => 'NFS'.$key,
                'xLocEmi' => $city,
                'xLocPrestacao' => (string) ($fields['serv']['locPrest']['cLocPrestacao'] ?? $city),
                'nNFSe' => $number,
                'cLocIncid' => (string) ($fields['serv']['locPrest']['cLocPrestacao'] ?? $city),
                'xLocIncid' => (string) ($fields['serv']['locPrest']['cLocPrestacao'] ?? $city),
                'xTribNac' => (new \ChristianoErick\NfseNacional\Codes\ServiceCodes)->description((string) ($fields['serv']['cServ']['cTribNac'] ?? '')) ?? 'Serviço',
                'verAplic' => 'Simulacao',
                'ambGer' => 2,
                'tpEmis' => 1,
                'procEmi' => 1,
                'cStat' => 100,
                'dhProc' => $now->format('Y-m-d\TH:i:sP'),
                'nDFSe' => $number,
                'emit' => [
                    strlen($document) === 11 ? 'CPF' : 'CNPJ' => $document,
                    'xNome' => mb_substr($this->certificate()->holderName() ?: 'EMITENTE', 0, 300),
                    'enderNac' => ['xLgr' => '-', 'nro' => 'S/N', 'xBairro' => '-', 'cMun' => $city, 'UF' => self::state($city), 'CEP' => '00000000'],
                ],
                'valores' => ['vLiq' => $amount],
                'xOutInf' => self::NOTICE,
            ],
        ]);

        $dpsDom = new \DOMDocument;
        $dpsDom->loadXML($signedDps);
        $nfse->getElementsByTagNameNS(SchemaMap::NAMESPACE, 'infNFSe')->item(0)->appendChild($nfse->importNode($dpsDom->documentElement, true));

        (new XmlSigner('sha256'))->sign($nfse, 'infNFSe', $this->certificate());

        return NfseClient::withDeclaration($nfse);
    }

    /** Chave de 50 posições com o leiaute oficial (ambiente gerador 2). */
    private static function accessKey(string $city, string $document, string $number, CarbonImmutable $at): string
    {
        $base = $city.'2'.(strlen($document) === 11 ? '1' : '2').str_pad($document, 14, '0', STR_PAD_LEFT)
            .str_pad($number, 13, '0', STR_PAD_LEFT).$at->format('ym').str_pad((string) random_int(0, 999_999_999), 9, '0', STR_PAD_LEFT);

        $sum = 0;
        $weight = 2;

        for ($i = strlen($base) - 1; $i >= 0; $i--) {
            $sum += (ctype_digit($base[$i]) ? (int) $base[$i] : ord($base[$i]) - 48) * $weight;
            $weight = $weight === 9 ? 2 : $weight + 1;
        }

        $digit = 11 - ($sum % 11);

        return $base.($digit >= 10 ? '0' : (string) $digit);
    }

    private static function state(string $city): string
    {
        $states = ['11' => 'RO', '12' => 'AC', '13' => 'AM', '14' => 'RR', '15' => 'PA', '16' => 'AP', '17' => 'TO', '21' => 'MA', '22' => 'PI', '23' => 'CE', '24' => 'RN', '25' => 'PB', '26' => 'PE', '27' => 'AL', '28' => 'SE', '29' => 'BA', '31' => 'MG', '32' => 'ES', '33' => 'RJ', '35' => 'SP', '41' => 'PR', '42' => 'SC', '43' => 'RS', '50' => 'MS', '51' => 'MT', '52' => 'GO', '53' => 'DF'];

        return $states[substr($city, 0, 2)] ?? 'DF';
    }
}
