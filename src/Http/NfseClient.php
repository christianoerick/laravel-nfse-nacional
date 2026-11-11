<?php

namespace ChristianoErick\NfseNacional\Http;

use ChristianoErick\NfseNacional\Certificate\Certificate;
use ChristianoErick\NfseNacional\Codes\ServiceCodes;
use ChristianoErick\NfseNacional\Dps\Dps;
use ChristianoErick\NfseNacional\Enums\CancellationReason;
use ChristianoErick\NfseNacional\Enums\Environment;
use ChristianoErick\NfseNacional\Events\EventRequest;
use ChristianoErick\NfseNacional\Exceptions\ConnectionException;
use ChristianoErick\NfseNacional\Exceptions\NotFoundException;
use ChristianoErick\NfseNacional\Exceptions\RejectedException;
use ChristianoErick\NfseNacional\Nfse\NfseDocument;
use ChristianoErick\NfseNacional\Support\Layout;
use ChristianoErick\NfseNacional\Support\ProcessingMessage;
use ChristianoErick\NfseNacional\Xml\SchemaValidator;
use ChristianoErick\NfseNacional\Xml\XmlBuilder;
use ChristianoErick\NfseNacional\Xml\XmlSigner;
use Illuminate\Http\Client\ConnectionException as HttpConnectionException;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;

/**
 * Cliente do Sistema Nacional NFS-e para um certificado (mTLS) e um
 * ambiente. Sefin Nacional: emissão, consulta, DPS e eventos. ADN: DANFSe,
 * parâmetros municipais e distribuição.
 *
 * Erros: RejectedException (regra de negócio, não reenviar igual),
 * ConnectionException (técnico, dá para repetir — antes consulte a DPS),
 * NotFoundException (consulta sem resultado).
 */
class NfseClient
{
    private ?TemporaryCredentials $credentials = null;

    private readonly Layout $layout;

    /**
     * @param  array<string, mixed>  $config  config('nfse-nacional')
     */
    public function __construct(
        private readonly Certificate $certificate,
        private readonly Environment $environment,
        private readonly array $config,
        private readonly HttpFactory $http,
    ) {
        $this->layout = Layout::resolve($config['layout'] ?? 'auto', $environment, null, $config['layout_switch'] ?? null);
    }

    /** true no SimulatedNfseClient (nada vai para o Sistema Nacional). */
    public function isSimulated(): bool
    {
        return false;
    }

    /** Leiaute usado pela DPS e pelos eventos deste cliente (ambiente + data). */
    public function layout(): Layout
    {
        return $this->layout;
    }

    public function environment(): Environment
    {
        return $this->environment;
    }

    public function certificate(): Certificate
    {
        return $this->certificate;
    }

    // ================================================================ DPS / NFS-e

    /**
     * Completa, valida no XSD, assina e envia a DPS. Devolve a NFS-e gerada.
     */
    public function emit(Dps $dps): EmissionResult
    {
        $signed = $this->signDps($dps);

        $response = $this->send('post', $this->sefin('nfse'), ['dpsXmlGZipB64' => self::pack($signed)]);
        $data = (array) $response->json();

        if (empty($data['nfseXmlGZipB64'])) {
            throw new RejectedException(ProcessingMessage::listFrom($data['erros'] ?? $data['erro'] ?? []) ?: [new ProcessingMessage(null, 'Resposta sem o XML da NFS-e.')], $response->status(), $data);
        }

        return new EmissionResult(
            nfse: NfseDocument::fromGzipBase64($data['nfseXmlGZipB64']),
            signedDpsXml: $signed,
            alerts: ProcessingMessage::listFrom($data['alertas'] ?? []),
            response: array_diff_key($data, ['nfseXmlGZipB64' => true]),
        );
    }

    /**
     * Emite com segurança contra resposta perdida: se a DPS (mesmo Id) já
     * virou nota, devolve a existente em vez de reenviar.
     */
    public function emitOrRecover(Dps $dps): EmissionResult
    {
        $dps = $this->prepare($dps);
        $existing = $this->findByDps($dps->id());

        if ($existing !== null) {
            return new EmissionResult($existing, signedDpsXml: '', recovered: true);
        }

        return $this->emit($dps);
    }

    /** XML da DPS pronto para envio (completo, validado e assinado). */
    public function signDps(Dps $dps): string
    {
        $dom = (new XmlBuilder($this->layout))->build('DPS', $this->prepare($dps)->toArray($this->layout));

        if ($this->config['validate_schema'] ?? true) {
            (new SchemaValidator($this->layout))->validate($dom);
        }

        // Com a declaração XML (encoding UTF-8): sem ela o Sefin recusa (E1229).
        return self::withDeclaration($this->signer()->sign($dom, 'infDPS', $this->certificate));
    }

    /**
     * Confere a DPS sem emitir nada: certificado, XSD do leiaute em uso,
     * assinatura e, só com consultas de leitura ao Sistema Nacional,
     * convênio do município, código de serviço/alíquota e se a DPS já virou
     * nota. Cada item: ok (true), falha (false) ou aviso (null).
     *
     * @return list<array{check: string, ok: bool|null, message: string}>
     */
    public function check(Dps $dps): array
    {
        $results = [];
        $add = function (string $check, ?bool $ok, string $message) use (&$results): void {
            $results[] = ['check' => $check, 'ok' => $ok, 'message' => $message];
        };

        $prepared = $this->prepare($dps);
        $fields = $prepared->fields();
        $issuerDocument = (string) ($fields['prest']['CNPJ'] ?? $fields['prest']['CPF'] ?? '');

        $add('certificado', ! $this->certificate->isExpired(), $this->certificate->isExpired()
            ? 'Certificado vencido em '.$this->certificate->validTo()->format('d/m/Y').'.'
            : 'Válido até '.$this->certificate->validTo()->format('d/m/Y').' ('.$this->certificate->holderName().').');

        if ($issuerDocument !== '' && $this->certificate->document() && $this->certificate->document() !== $issuerDocument) {
            $add('certificado', null, "O certificado é do documento {$this->certificate->document()} e a DPS é do prestador {$issuerDocument} (só vale se for procurador).");
        }

        try {
            $signed = $this->signDps($prepared);
            $add('leiaute', true, 'DPS válida no XSD oficial ('.$this->layout->label().').');
            $add('assinatura', XmlSigner::verify($signed), XmlSigner::verify($signed) ? 'Assinatura XMLDSig confere.' : 'Assinatura não confere.');
        } catch (\ChristianoErick\NfseNacional\Exceptions\SchemaValidationException $e) {
            $add('leiaute', false, $e->getMessage());
        } catch (\ChristianoErick\NfseNacional\Exceptions\InvalidDataException $e) {
            $add('leiaute', false, $e->getMessage());
        }

        $city = (string) ($fields['cLocEmi'] ?? '');

        try {
            $agreement = $this->municipalAgreement($city);
            $add('convênio', true, "Município {$city} ativo no Sistema Nacional (".$this->environment->label().').');
        } catch (\Throwable $e) {
            $add('convênio', false, "Município {$city}: ".$e->getMessage().' Nesse ambiente a emissão será recusada (E0037).');
        }

        $incidence = (string) ($fields['serv']['locPrest']['cLocPrestacao'] ?? $city);
        $national = (string) ($fields['serv']['cServ']['cTribNac'] ?? '');
        $municipal = $fields['serv']['cServ']['cTribMun'] ?? null;

        if ($national !== '') {
            try {
                $competence = (string) ($fields['dCompet'] instanceof \DateTimeInterface ? $fields['dCompet']->format('Y-m-d') : $fields['dCompet']);
                $rate = $this->municipalRate($incidence, $national, $competence, $municipal);
                $code = ServiceCodes::parameterCode($national, $municipal);
                $found = $rate['aliquotas'][$code] ?? [];
                $found === []
                    ? $add('serviço', false, "Código {$code} não está administrado pelo município {$incidence} nessa competência (E0312). ".($municipal ? '' : 'Informe o código municipal (cTribMun).'))
                    : $add('serviço', true, "Código {$code} ativo em {$incidence}, alíquota ".($found[0]['Aliq'] ?? '?').'%.');
            } catch (\Throwable $e) {
                $add('serviço', null, 'Não foi possível conferir o código de serviço: '.$e->getMessage());
            }
        }

        try {
            $exists = $this->dpsExists($prepared->id());
            $add('numeração', $exists ? null : true, $exists
                ? 'Já existe NFS-e para esta DPS ('.$prepared->id().'): o envio recupera a nota em vez de criar outra.'
                : 'DPS '.$prepared->id().' ainda não usada.');
        } catch (\Throwable $e) {
            $add('numeração', null, 'Não foi possível conferir a DPS: '.$e->getMessage());
        }

        if (isset($fields['prest']['IM'])) {
            $add('inscrição municipal', null, 'A DPS leva a inscrição municipal do prestador: municípios sem dados complementares no CNC recusam (E0120, ex.: Maceió).');
        }

        return $results;
    }

    /** DPS com ambiente, versão do aplicativo e data de emissão preenchidos. */
    public function prepare(Dps $dps): Dps
    {
        return $dps->withDefaults($this->environment, $this->applicationVersion());
    }

    /** HEAD /dps/{id}: já existe NFS-e para esta DPS? (qualquer certificado válido) */
    public function dpsExists(string $dpsId): bool
    {
        $response = $this->send('head', $this->sefin('dps/'.$dpsId), allowNotFound: true);

        return $response->successful();
    }

    /** GET /dps/{id}: chave de acesso da NFS-e gerada pela DPS, ou null. */
    public function accessKeyForDps(string $dpsId): ?string
    {
        $response = $this->send('get', $this->sefin('dps/'.$dpsId), allowNotFound: true);

        return $response->status() === 404 ? null : ($response->json('chaveAcesso') ?: null);
    }

    /** NFS-e gerada pela DPS, ou null. */
    public function findByDps(string $dpsId): ?NfseDocument
    {
        $accessKey = $this->accessKeyForDps($dpsId);

        return $accessKey ? $this->find($accessKey) : null;
    }

    /** GET /nfse/{chave}. */
    public function get(string $accessKey): NfseDocument
    {
        return $this->find($accessKey) ?? throw new NotFoundException("NFS-e {$accessKey} não encontrada.");
    }

    public function find(string $accessKey): ?NfseDocument
    {
        $response = $this->send('get', $this->sefin('nfse/'.$accessKey), allowNotFound: true);

        if ($response->status() === 404 || ! $response->json('nfseXmlGZipB64')) {
            return null;
        }

        return NfseDocument::fromGzipBase64($response->json('nfseXmlGZipB64'));
    }

    // ================================================================ eventos

    /** Assina e registra um evento (POST /nfse/{chave}/eventos). */
    public function registerEvent(EventRequest $event): EventResult
    {
        $signed = $this->signEvent($event);

        $response = $this->send('post', $this->sefin('nfse/'.$event->accessKey.'/eventos'), ['pedidoRegistroEventoXmlGZipB64' => self::pack($signed)]);
        $data = (array) $response->json();

        return new EventResult(
            eventXml: isset($data['eventoXmlGZipB64']) ? self::unpack((string) $data['eventoXmlGZipB64']) : '',
            signedRequestXml: $signed,
            response: array_diff_key($data, ['eventoXmlGZipB64' => true]),
        );
    }

    public function signEvent(EventRequest $event): string
    {
        $dom = (new XmlBuilder($this->layout))->build('pedRegEvento', $event->toArray($this->environment, $this->applicationVersion(), $this->certificate->document()));

        if ($this->config['validate_schema'] ?? true) {
            (new SchemaValidator($this->layout))->validate($dom);
        }

        return self::withDeclaration($this->signer()->sign($dom, 'infPedReg', $this->certificate));
    }

    /** Cancela a NFS-e (e101101). Motivo: 1 erro na emissão, 2 serviço não prestado, 9 outros. */
    public function cancel(string $accessKey, CancellationReason|string|int $reason, string $description): EventResult
    {
        return $this->registerEvent(EventRequest::cancellation($accessKey, $reason, $description));
    }

    /**
     * Evento pelo tipo e sequência (GET /nfse/{chave}/eventos/{tipo}/{seq}).
     * Tipo com ou sem o "e": 101101 ou e101101.
     */
    public function event(string $accessKey, string $type, int $sequence = 1): ?EventResult
    {
        // O código vai sem o "e" (101103); com ele o Sefin responde 404.
        $response = $this->send('get', $this->sefin('nfse/'.$accessKey.'/eventos/'.ltrim($type, 'e').'/'.$sequence), allowNotFound: true);

        if ($response->status() === 404) {
            return null;
        }

        $data = (array) $response->json();
        // Resposta real: lista "eventos" com arquivoXml (o Swagger fala em eventoXmlGZipB64).
        $items = $data['eventos'] ?? null;
        $item = is_array($items) ? (array_is_list($items) ? ($items[0] ?? null) : $items) : null;
        $payload = $item['arquivoXml'] ?? $item['ArquivoXml'] ?? $data['eventoXmlGZipB64'] ?? null;

        if (! $payload) {
            return null;
        }

        return new EventResult(self::unpack((string) $payload), response: array_diff_key($data, ['eventos' => true]) + ['evento' => array_diff_key((array) $item, ['arquivoXml' => true, 'ArquivoXml' => true])]);
    }

    /**
     * Todos os eventos da nota pelo ADN (contribuintes/NFSe/{chave}/Eventos).
     *
     * @return list<array<string, mixed>>
     */
    public function events(string $accessKey): array
    {
        $response = $this->send('get', $this->adn('contribuintes/NFSe/'.$accessKey.'/Eventos'), allowNotFound: true);

        if ($response->status() === 404) {
            return [];
        }

        return array_map(fn (array $item) => $item + [
            'xml' => isset($item['ArquivoXml']) ? self::unpack((string) $item['ArquivoXml']) : (isset($item['arquivoXml']) ? self::unpack((string) $item['arquivoXml']) : null),
        ], (array) ($response->json('LoteDFe') ?? []));
    }

    // ================================================================ ADN

    /** PDF oficial do DANFSe (ADN /danfse/{chave}). */
    public function danfse(string $accessKey): string
    {
        $response = $this->send('get', $this->adn('danfse/'.$accessKey), allowNotFound: true, accept: 'application/pdf');

        if ($response->status() === 404) {
            throw new NotFoundException("DANFSe da NFS-e {$accessKey} não encontrado.");
        }

        return $response->body();
    }

    /**
     * Documentos fiscais a partir de um NSU (ADN contribuintes/DFe/{NSU}),
     * até 100 por chamada.
     *
     * @return array<string, mixed>
     */
    public function distribution(int $nsu, bool $lote = true): array
    {
        $response = $this->send('get', $this->adn('contribuintes/DFe/'.$nsu).($lote ? '?lote=true' : ''), allowNotFound: true);

        if ($response->status() === 404) {
            return ['LoteDFe' => []];
        }

        $data = (array) $response->json();
        $data['LoteDFe'] = array_map(fn (array $item) => $item + [
            'xml' => isset($item['ArquivoXml']) ? self::unpack((string) $item['ArquivoXml']) : null,
        ], (array) ($data['LoteDFe'] ?? []));

        return $data;
    }

    /** Convênio do município com o Sistema Nacional. */
    public function municipalAgreement(string $cityIbgeCode): MunicipalAgreement
    {
        $data = (array) $this->send('get', $this->adn('parametrizacao/'.$cityIbgeCode.'/convenio'))->json();

        return new MunicipalAgreement($cityIbgeCode, (array) ($data['parametrosConvenio'] ?? []), $data['mensagem'] ?? null);
    }

    /**
     * Alíquota do serviço no município numa competência.
     *
     * @param  string  $serviceCode  código nacional + municipal, em qualquer formato (01.05.01.001, 010501001, ou 010501 com $municipalCode)
     * @param  string  $competence  AAAA-MM-DD
     * @return array<string, mixed> ['aliquotas' => [código => [...]]] (vazio = serviço não administrado pelo município)
     */
    public function municipalRate(string $cityIbgeCode, string $serviceCode, string $competence, ?string $municipalCode = null): array
    {
        return $this->parameters("{$cityIbgeCode}/".ServiceCodes::parameterCode($serviceCode, $municipalCode)."/{$competence}/aliquota");
    }

    /** @return array<string, mixed> */
    public function municipalRateHistory(string $cityIbgeCode, string $serviceCode, ?string $municipalCode = null): array
    {
        return $this->parameters("{$cityIbgeCode}/".ServiceCodes::parameterCode($serviceCode, $municipalCode).'/historicoaliquotas');
    }

    /** @return array<string, mixed> */
    public function municipalBenefit(string $cityIbgeCode, string $benefitNumber, string $competence): array
    {
        return $this->parameters("{$cityIbgeCode}/{$benefitNumber}/{$competence}/beneficio");
    }

    /** @return array<string, mixed> */
    public function specialRegimes(string $cityIbgeCode, string $serviceCode, string $competence, ?string $municipalCode = null): array
    {
        return $this->parameters("{$cityIbgeCode}/".ServiceCodes::parameterCode($serviceCode, $municipalCode)."/{$competence}/regimes_especiais");
    }

    /** @return array<string, mixed> */
    public function withholdings(string $cityIbgeCode, string $competence): array
    {
        return $this->parameters("{$cityIbgeCode}/{$competence}/retencoes");
    }

    // ================================================================ interno

    /** @return array<string, mixed> */
    private function parameters(string $path): array
    {
        $response = $this->send('get', $this->adn('parametrizacao/'.$path), allowNotFound: true);

        return $response->status() === 404 ? [] : (array) $response->json();
    }

    private function applicationVersion(): string
    {
        return mb_substr((string) ($this->config['application_version'] ?? 'LaravelNfseNacional1'), 0, 20);
    }

    private function signer(): XmlSigner
    {
        return new XmlSigner((string) ($this->config['signature_algorithm'] ?? 'sha1'));
    }

    private function sefin(string $path): string
    {
        return rtrim((string) $this->config['urls'][$this->environment->key()]['sefin'], '/').'/'.$path;
    }

    private function adn(string $path): string
    {
        return rtrim((string) $this->config['urls'][$this->environment->key()]['adn'], '/').'/'.$path;
    }

    /** XML completo com a declaração UTF-8 (exigida pelo Sefin: E1229). */
    public static function withDeclaration(\DOMDocument $dom): string
    {
        $dom->encoding = 'UTF-8';

        return '<?xml version="1.0" encoding="UTF-8"?>'.$dom->saveXML($dom->documentElement);
    }

    /**
     * XML de um campo da API: gzip em base64 (às vezes em base64 de novo,
     * como o arquivoXml da consulta de eventos) ou XML puro.
     */
    public static function unpack(string $payload): string
    {
        $value = trim($payload);

        for ($i = 0; $i < 3; $i++) {
            if (str_starts_with($value, '<')) {
                return $value;
            }

            $decoded = base64_decode($value, true);

            if ($decoded === false) {
                break;
            }

            $value = str_starts_with($decoded, "\x1f\x8b") ? (string) gzdecode($decoded) : trim($decoded);
        }

        return str_starts_with($value, '<') ? $value : '';
    }

    public static function pack(string $xml): string
    {
        return base64_encode((string) gzencode($xml));
    }

    private function request(string $accept = 'application/json'): PendingRequest
    {
        $this->credentials ??= new TemporaryCredentials($this->certificate, $this->config['temp_path'] ?? null);
        $http = $this->config['http'] ?? [];
        $verify = $http['verify'] ?? true;

        return $this->http->withOptions([
            'cert' => $this->credentials->certificatePath,
            'ssl_key' => $this->credentials->keyPath,
            'verify' => is_string($verify) && in_array(strtolower($verify), ['true', 'false', '1', '0'], true) ? filter_var($verify, FILTER_VALIDATE_BOOLEAN) : $verify,
            'connect_timeout' => (int) ($http['connect_timeout'] ?? 15),
        ])
            ->timeout((int) ($http['timeout'] ?? 60))
            ->withUserAgent('laravel-nfse-nacional')
            ->accept($accept);
    }

    /**
     * @param  array<string, mixed>|null  $body
     */
    private function send(string $method, string $url, ?array $body = null, bool $allowNotFound = false, string $accept = 'application/json'): Response
    {
        $http = $this->config['http'] ?? [];
        // POST não repete sozinho: a nota pode ter sido gerada (use emitOrRecover).
        $attempts = $method === 'post' ? 1 : 1 + (int) ($http['retries'] ?? 2);
        $last = null;

        for ($attempt = 1; $attempt <= $attempts; $attempt++) {
            try {
                $request = $this->request($accept);
                $response = match ($method) {
                    'post' => $request->asJson()->post($url, $body ?? []),
                    'head' => $request->head($url),
                    default => $request->get($url),
                };
            } catch (HttpConnectionException $e) {
                $last = new ConnectionException('Sem resposta do Sistema Nacional NFS-e: '.$e->getMessage(), null, $e);
                $this->pause($attempt, $attempts, $http);

                continue;
            }

            if ($response->successful() || ($allowNotFound && $response->status() === 404)) {
                return $response;
            }

            $status = $response->status();
            $data = (array) $response->json();
            $messages = ProcessingMessage::listFrom($data['erros'] ?? $data['Erros'] ?? $data['erro'] ?? $data['Erro'] ?? []);

            if ($status === 429 || $status >= 500) {
                $last = new ConnectionException(trim("HTTP {$status} do Sistema Nacional NFS-e. ".implode(' | ', array_map('strval', $messages))), $status);
                $this->pause($attempt, $attempts, $http);

                continue;
            }

            if ($messages === [] && ($data['mensagem'] ?? null)) {
                $messages = [new ProcessingMessage(null, (string) $data['mensagem'])];
            }

            if ($messages === []) {
                $messages = [new ProcessingMessage((string) $status, $status === 401 || $status === 403
                    ? 'Acesso negado: o certificado não pertence a um dos participantes da nota ou não foi aceito.'
                    : 'Requisição recusada (HTTP '.$status.').')];
            }

            throw new RejectedException($messages, $status, $data);
        }

        throw $last ?? new ConnectionException('Falha ao falar com o Sistema Nacional NFS-e.');
    }

    /**
     * @param  array<string, mixed>  $http
     */
    private function pause(int $attempt, int $attempts, array $http): void
    {
        if ($attempt < $attempts) {
            usleep((int) ($http['retry_sleep_ms'] ?? 1000) * 1000 * $attempt);
        }
    }
}
