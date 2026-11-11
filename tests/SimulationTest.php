<?php

namespace ChristianoErick\NfseNacional\Tests;

use ChristianoErick\NfseNacional\Dps\Dps;
use ChristianoErick\NfseNacional\Events\EventRequest;
use ChristianoErick\NfseNacional\Facades\NfseNacional;
use ChristianoErick\NfseNacional\Http\SimulatedNfseClient;
use ChristianoErick\NfseNacional\Xml\SchemaValidator;
use ChristianoErick\NfseNacional\Xml\XmlSigner;
use Illuminate\Support\Facades\Http;

class SimulationTest extends TestCase
{
    public function test_simulated_flow_without_network(): void
    {
        Http::preventStrayRequests();

        $client = NfseNacional::client($this->certificate(), 'restricted', simulate: true);
        $this->assertInstanceOf(SimulatedNfseClient::class, $client);

        $fields = $this->dpsFields(['nDPS' => random_int(1, 99999)]);
        unset($fields['tpAmb'], $fields['dhEmi'], $fields['verAplic']);
        $dps = Dps::make($fields);

        $result = $client->emitOrRecover($dps);
        $nfse = $result->nfse;

        // NFS-e simulada: válida no XSD do leiaute em uso, assinada, marcada.
        (new SchemaValidator($client->layout()))->validate($result->xml());
        $this->assertTrue($nfse->hasValidSignature());
        $this->assertTrue(XmlSigner::verify($result->signedDpsXml));
        $this->assertStringStartsWith('SIMULAÇÃO', $nfse->get('xOutInf'));
        $this->assertSame(50, strlen($nfse->accessKey()));
        $this->assertSame('7500.00', $nfse->serviceAmount());

        // Reenvio da mesma DPS recupera a mesma nota.
        $again = $client->emitOrRecover($dps);
        $this->assertTrue($again->recovered);
        $this->assertSame($nfse->accessKey(), $again->accessKey());

        $event = $client->registerEvent(EventRequest::cancellation($nfse->accessKey(), '1', 'Teste de cancelamento simulado.'));
        $this->assertStringContainsString('<e101101>', $event->eventXml);

        // Validação sem emitir também roda sem rede.
        $checks = collect($client->check($dps->with('nDPS', 99999999)));
        $this->assertTrue($checks->firstWhere('check', 'leiaute')['ok']);
        $this->assertTrue($checks->firstWhere('check', 'convênio')['ok']);
    }

    public function test_simulated_nfse_is_valid_in_both_layouts(): void
    {
        foreach (['v1.01', 'nt009'] as $layout) {
            config(['nfse-nacional.layout' => $layout]);
            $client = NfseNacional::client($this->certificate(), 'production', simulate: true);
            $result = $client->emit(Dps::make($this->dpsFields(['nDPS' => random_int(1, 99999)])));

            (new SchemaValidator($client->layout()))->validate($result->xml());
            $this->assertSame($layout, $client->layout()->value);
        }
    }

    public function test_check_reports_inactive_municipality_and_unknown_service(): void
    {
        Http::fake([
            '*/parametrizacao/2704302/convenio' => Http::response(['erro' => ['codigo' => 'E404', 'descricao' => 'O convênio do o município <Maceió/AL> ainda não está ativo no Sistema Nacional da NFS-e']], 404),
            '*/parametrizacao/2704302/01.05.01.001/*/aliquota' => Http::response([], 404),
            '*/dps/*' => Http::response([], 404),
        ]);

        $checks = collect(NfseNacional::client($this->certificate(), 'restricted')->check(Dps::make($this->dpsFields())));

        $this->assertTrue($checks->firstWhere('check', 'leiaute')['ok']);
        $this->assertFalse($checks->firstWhere('check', 'serviço')['ok']);
        $this->assertStringContainsString('01.05.01.001', $checks->firstWhere('check', 'serviço')['message']);
        $this->assertTrue($checks->firstWhere('check', 'numeração')['ok']);
        Http::assertSent(fn ($r) => str_contains($r->url(), '/parametrizacao/2704302/01.05.01.001/'));
    }
}
