<?php

namespace ChristianoErick\NfseNacional\Tests;

use ChristianoErick\NfseNacional\Danfse\DanfseRenderer;
use ChristianoErick\NfseNacional\Nfse\NfseDocument;
use ChristianoErick\NfseNacional\Support\AccessKey;
use ChristianoErick\NfseNacional\Xml\SchemaValidator;

class NfseDocumentTest extends TestCase
{
    use BuildsNfse;

    public function test_reads_summary_and_original_dps(): void
    {
        $nfse = NfseDocument::fromXml($this->makeNfseXml($this->certificate()));

        $this->assertSame('27043022211222333000181000000000000126113381019914', $nfse->accessKey());
        $this->assertSame('1', $nfse->number());
        $this->assertSame('7500.00', $nfse->serviceAmount());
        $this->assertSame('CLIENTE EXEMPLO LTDA', $nfse->summary()['taker_name']);
        $this->assertTrue($nfse->hasValidSignature());

        // Duplicar: a DPS da nota vira base de uma nova, com outro número e valor.
        $copy = $nfse->dps()->with('nDPS', 2)->with('valores.vServPrest.vServ', 990);
        (new SchemaValidator)->validate($copy->toXml());
        $this->assertStringEndsWith('000000000000002', $copy->id());
    }

    public function test_access_key_parts(): void
    {
        $key = AccessKey::parse('27043022211222333000181000000000000126113381019914');

        $this->assertSame('2704302', $key->city);
        $this->assertSame('2', $key->generatorEnvironment);
        $this->assertSame('11222333000181', $key->document);
        $this->assertSame('1', $key->number);
        $this->assertSame('2611', $key->yearMonth);
        $this->assertSame('https://www.nfse.gov.br/ConsultaPublica?tpc=1&chave=27043022211222333000181000000000000126113381019914', $key->publicUrl());
    }

    public function test_renders_danfse_pdf(): void
    {
        $nfse = NfseDocument::fromXml($this->makeNfseXml($this->certificate()));
        $renderer = new DanfseRenderer;

        $data = $renderer->data($nfse);
        $this->assertSame('11.222.333/0001-81', $data['provider']['document']);
        $this->assertSame('Maceió / AL', $data['taker']['city']);
        $this->assertSame('01.05.01 / 001', $data['service_code']);
        $this->assertSame('R$ 7.500,00', $data['service_amount']);
        $this->assertSame('Não Retido', $data['issqn_withholding']);
        $this->assertSame('(82) 99999-0000', $data['provider']['phone']);
        // Igual ao DANFSe oficial (conferido com a NFS-e nº 2 de 11/11/2026).
        $this->assertSame('1', $data['dps_series']);
        $this->assertSame('Operação Tributável', $data['issqn_taxation']);
        $this->assertStringEndsWith('pelo Simples Nacional', $data['simples_regime']);
        $this->assertStringEndsWith('Federais: -; Estaduais: -; Municipais: -;', $data['additional_info']);

        $pdf = $renderer->render($nfse);
        $this->assertStringStartsWith('%PDF', $pdf);

        if ($out = getenv('DANFSE_OUT')) {
            file_put_contents($out, $pdf);
        }
    }
}
