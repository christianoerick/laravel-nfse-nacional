<?php

namespace ChristianoErick\NfseNacional\Tests;

use ChristianoErick\NfseNacional\Dps\Dps;
use ChristianoErick\NfseNacional\Enums\CancellationReason;
use ChristianoErick\NfseNacional\Events\EventRequest;
use ChristianoErick\NfseNacional\Exceptions\ConnectionException;
use ChristianoErick\NfseNacional\Exceptions\RejectedException;
use ChristianoErick\NfseNacional\Facades\NfseNacional;
use ChristianoErick\NfseNacional\Http\NfseClient;
use ChristianoErick\NfseNacional\Xml\SchemaValidator;
use ChristianoErick\NfseNacional\Xml\XmlSigner;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

class ClientTest extends TestCase
{
    use BuildsNfse;

    private const KEY = '27043022211222333000181000000000000126113381019914';

    protected function setUp(): void
    {
        parent::setUp();

        config(['nfse-nacional.http.retry_sleep_ms' => 0]);
    }

    private function client(): NfseClient
    {
        return NfseNacional::client($this->certificate(), 'restricted');
    }

    public function test_emits_and_returns_the_generated_nfse(): void
    {
        $certificate = $this->certificate();
        $nfseXml = $this->makeNfseXml($certificate);

        Http::fake([
            'sefin.producaorestrita.nfse.gov.br/SefinNacional/nfse' => Http::response([
                'tipoAmbiente' => 2,
                'idDps' => 'DPS270430221122233300018100001000000000000001',
                'chaveAcesso' => self::KEY,
                'nfseXmlGZipB64' => NfseClient::pack($nfseXml),
                'alertas' => [['codigo' => 'A0001', 'descricao' => 'Alerta de teste']],
            ], 201),
        ]);

        $fields = $this->dpsFields();
        unset($fields['tpAmb'], $fields['dhEmi'], $fields['verAplic']);

        $result = NfseNacional::client($certificate, 'restricted')->emit(Dps::make($fields));

        $this->assertSame(self::KEY, $result->accessKey());
        $this->assertSame('A0001', $result->alerts[0]->code);
        $this->assertTrue(XmlSigner::verify($result->signedDpsXml));

        Http::assertSent(function (Request $request) {
            $dps = gzdecode(base64_decode($request['dpsXmlGZipB64']));
            (new SchemaValidator)->validate($dps);

            return $request->method() === 'POST'
                && str_contains($dps, '<tpAmb>2</tpAmb>')
                && str_contains($dps, '<verAplic>LaravelNfseNacional1</verAplic>');
        });
    }

    public function test_rejection_keeps_official_codes(): void
    {
        Http::fake(['*/nfse' => Http::response([
            'erros' => [['codigo' => 'E0014', 'descricao' => 'DPS já utilizada', 'complemento' => 'série 1 número 1']],
        ], 400)]);

        try {
            $this->client()->emit(Dps::make($this->dpsFields()));
            $this->fail('Deveria recusar.');
        } catch (RejectedException $e) {
            $this->assertSame(['E0014'], $e->codes());
            $this->assertStringContainsString('série 1 número 1', $e->getMessage());
        }
    }

    public function test_server_error_is_a_connection_exception_and_post_is_not_retried(): void
    {
        Http::fake(['*/nfse' => Http::response(['erros' => []], 503)]);

        $this->expectException(ConnectionException::class);

        try {
            $this->client()->emit(Dps::make($this->dpsFields()));
        } finally {
            Http::assertSentCount(1);
        }
    }

    public function test_emit_or_recover_returns_existing_note_without_resending(): void
    {
        $certificate = $this->certificate();

        Http::fake([
            '*/dps/*' => Http::response(['chaveAcesso' => self::KEY]),
            '*/nfse/'.self::KEY => Http::response(['nfseXmlGZipB64' => NfseClient::pack($this->makeNfseXml($certificate))]),
        ]);

        $result = NfseNacional::client($certificate, 'restricted')->emitOrRecover(Dps::make($this->dpsFields()));

        $this->assertTrue($result->recovered);
        $this->assertSame(self::KEY, $result->accessKey());
        Http::assertNotSent(fn (Request $request) => $request->method() === 'POST');
    }

    public function test_lookups_return_null_when_not_found(): void
    {
        Http::fake(['*' => Http::response(['erro' => ['codigo' => 'E404', 'descricao' => 'Não encontrado']], 404)]);

        $client = $this->client();

        $this->assertNull($client->findByDps('DPS270430221122233300018100001000000000000001'));
        $this->assertFalse($client->dpsExists('DPS270430221122233300018100001000000000000001'));
        $this->assertNull($client->find(self::KEY));
    }

    public function test_cancellation_event_is_signed_and_xsd_valid(): void
    {
        Http::fake(['*/eventos' => Http::response(['eventoXmlGZipB64' => NfseClient::pack('<evento/>'), 'dataHoraProcessamento' => '2026-11-11T17:00:00-03:00'], 201)]);

        $result = $this->client()->cancel(self::KEY, CancellationReason::IssueError, 'Valor informado errado na emissão.');

        $this->assertSame('<evento/>', $result->eventXml);
        (new SchemaValidator)->validate($result->signedRequestXml);
        $this->assertTrue(XmlSigner::verify($result->signedRequestXml));
        $this->assertStringContainsString('<CNPJAutor>'.self::PROVIDER_CNPJ.'</CNPJAutor>', $result->signedRequestXml);
        $this->assertStringContainsString('Id="PRE'.self::KEY.'101101"', $result->signedRequestXml);
    }

    public function test_substitution_event_is_xsd_valid(): void
    {
        $xml = $this->client()->signEvent(EventRequest::substitution(self::KEY, '27043022211222333000181000000000000226113381019915', 99, 'Substituída por nota com valor correto.'));

        (new SchemaValidator)->validate($xml);
        $this->assertStringContainsString('<chSubstituta>', $xml);
    }

    public function test_municipal_agreement_and_danfse(): void
    {
        Http::fake([
            'adn.producaorestrita.nfse.gov.br/parametrizacao/2704302/convenio' => Http::response([
                'mensagem' => 'ok',
                'parametrosConvenio' => ['aderenteAmbienteNacional' => 1, 'aderenteEmissorNacional' => 1, 'aderenteMAN' => 0],
            ]),
            'adn.producaorestrita.nfse.gov.br/danfse/*' => Http::response('%PDF-1.4 fake', 200, ['Content-Type' => 'application/pdf']),
        ]);

        $client = $this->client();

        $this->assertTrue($client->municipalAgreement('2704302')->usesNationalIssuer());
        $this->assertStringStartsWith('%PDF', $client->danfse(self::KEY));
    }
}
