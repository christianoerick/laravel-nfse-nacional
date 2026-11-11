<?php

namespace ChristianoErick\NfseNacional\Tests;

use ChristianoErick\NfseNacional\Dps\Dps;
use ChristianoErick\NfseNacional\Enums\Environment;
use ChristianoErick\NfseNacional\Enums\IssqnWithholding;
use ChristianoErick\NfseNacional\Exceptions\InvalidDataException;
use ChristianoErick\NfseNacional\Exceptions\SchemaValidationException;
use ChristianoErick\NfseNacional\Support\DpsId;
use ChristianoErick\NfseNacional\Xml\SchemaValidator;

class DpsTest extends TestCase
{
    public function test_builds_xsd_valid_dps_in_schema_order(): void
    {
        // Ordem embaralhada de propósito: o builder segue o XSD.
        $fields = $this->dpsFields();
        $fields = array_reverse($fields, true);

        $xml = Dps::make($fields)->toXml();

        (new SchemaValidator)->validate($xml);

        $this->assertStringContainsString('<infDPS Id="DPS270430221122233300018100001000000000000001">', $xml);
        $this->assertStringContainsString('<vServ>7500.00</vServ>', $xml);
        $this->assertStringContainsString('<serie>00001</serie>', $xml);
        $this->assertStringContainsString('<pTotTribSN>6.00</pTotTribSN>', $xml);
        $this->assertLessThan(strpos($xml, '<valores>'), strpos($xml, '<serv>'));
    }

    public function test_id_follows_official_rule(): void
    {
        $this->assertSame('DPS270430221122233300018100001000000000000027', DpsId::make('2704302', '11.222.333/0001-81', '1', 27));
        $this->assertSame('DPS270430210005299822472501000000000000000005', DpsId::make('2704302', '529.982.247-25', 1000, 5));

        $parsed = DpsId::parse('DPS270430221122233300018100001000000000000027');
        $this->assertSame('11222333000181', $parsed['document']);
        $this->assertSame('27', $parsed['number']);
    }

    public function test_accepts_enums_dates_and_numbers(): void
    {
        $xml = Dps::make($this->dpsFields([
            'tpAmb' => Environment::Restricted,
            'dCompet' => new \DateTimeImmutable('2026-10-19'),
            'valores' => ['trib' => ['tribMun' => ['tpRetISSQN' => IssqnWithholding::NotWithheld]]],
        ]))->toXml();

        (new SchemaValidator)->validate($xml);
        $this->assertStringContainsString('<dCompet>2026-10-19</dCompet>', $xml);
    }

    public function test_unknown_field_is_reported_with_allowed_keys(): void
    {
        $this->expectException(InvalidDataException::class);
        $this->expectExceptionMessage('DPS/infDPS/toma');

        Dps::make($this->dpsFields(['toma' => ['nome' => 'x']]))->toXml();
    }

    public function test_schema_errors_are_explained(): void
    {
        $xml = Dps::make($this->dpsFields(['serv' => ['cServ' => ['cTribNac' => '1']]]))->toXml();

        try {
            (new SchemaValidator)->validate($xml);
            $this->fail('Deveria recusar cTribNac com 1 dígito.');
        } catch (SchemaValidationException $e) {
            $this->assertStringContainsString('cTribNac', $e->getMessage());
        }
    }

    public function test_with_and_without_change_a_copy(): void
    {
        $dps = Dps::make($this->dpsFields());
        $changed = $dps->with('valores.vServPrest.vServ', 1200)->with('nDPS', 2)->without('toma.end.xCpl');

        $this->assertSame(7500, $dps->get('valores.vServPrest.vServ'));
        $this->assertSame(1200, $changed->get('valores.vServPrest.vServ'));
        $this->assertNull($changed->get('toma.end.xCpl'));
        $this->assertStringEndsWith('000000000000002', $changed->id());
    }

    public function test_defaults_fill_environment_version_and_emission_date(): void
    {
        $fields = $this->dpsFields();
        unset($fields['tpAmb'], $fields['verAplic'], $fields['dhEmi']);

        $dps = Dps::make($fields)->withDefaults(Environment::Production, 'App-1');

        $this->assertSame(Environment::Production, $dps->get('tpAmb'));
        $this->assertSame('App-1', $dps->get('verAplic'));
        (new SchemaValidator)->validate($dps->toXml());
    }

    public function test_reads_dps_back_from_xml(): void
    {
        $original = Dps::make($this->dpsFields());
        $copy = Dps::fromXml($original->toXml());

        $this->assertSame($original->id(), $copy->id());
        $this->assertSame('7500.00', $copy->get('valores.vServPrest.vServ'));
        (new SchemaValidator)->validate($copy->toXml());
    }

    public function test_duplicate_resets_emission_fields(): void
    {
        $original = Dps::make($this->dpsFields(['serie' => 70000, 'subst' => ['chSubstda' => str_repeat('1', 50), 'cMotivo' => '99', 'xMotivo' => 'Correção de valor da nota.']]));
        $copy = $original->duplicate(2, 10);

        $this->assertNull($copy->get('dhEmi'));
        $this->assertNull($copy->get('subst'));
        $this->assertSame(2, $copy->get('serie'));
        $this->assertStringEndsWith('00002000000000000010', $copy->id());
        $this->assertSame('CLIENTE EXEMPLO LTDA', $copy->get('toma.xNome'));
    }
}
