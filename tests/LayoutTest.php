<?php

namespace ChristianoErick\NfseNacional\Tests;

use Carbon\CarbonImmutable;
use ChristianoErick\NfseNacional\Dps\Dps;
use ChristianoErick\NfseNacional\Enums\Environment;
use ChristianoErick\NfseNacional\Exceptions\InvalidDataException;
use ChristianoErick\NfseNacional\Facades\NfseNacional;
use ChristianoErick\NfseNacional\Support\Layout;
use ChristianoErick\NfseNacional\Xml\SchemaValidator;

class LayoutTest extends TestCase
{
    public function test_auto_layout_switches_by_environment_and_date(): void
    {
        $this->assertSame(Layout::V101, Layout::resolve('auto', Environment::Production, CarbonImmutable::parse('2026-11-30 23:59', 'America/Sao_Paulo')));
        $this->assertSame(Layout::Nt009, Layout::resolve('auto', Environment::Production, CarbonImmutable::parse('2026-12-01 00:00', 'America/Sao_Paulo')));
        $this->assertSame(Layout::V101, Layout::resolve('auto', Environment::Restricted, CarbonImmutable::parse('2026-10-19', 'America/Sao_Paulo')));
        $this->assertSame(Layout::Nt009, Layout::resolve('auto', Environment::Restricted, CarbonImmutable::parse('2026-10-20', 'America/Sao_Paulo')));
        $this->assertSame(Layout::Nt009, Layout::resolve('nt009', Environment::Production, CarbonImmutable::parse('2026-01-01')));
        $this->assertSame(Layout::V101, Layout::resolve('auto', Environment::Production, CarbonImmutable::parse('2027-01-01'), ['production' => '2027-02-01']));
    }

    public function test_client_uses_configured_layout(): void
    {
        config(['nfse-nacional.layout' => 'nt009']);
        $this->assertSame(Layout::Nt009, NfseNacional::client($this->certificate(), 'production')->layout());

        config(['nfse-nacional.layout' => 'v1.01']);
        $this->assertSame(Layout::V101, NfseNacional::client($this->certificate(), 'restricted')->layout());
    }

    public function test_same_dps_is_valid_in_both_layouts(): void
    {
        $dps = Dps::make($this->dpsFields());

        foreach (Layout::cases() as $layout) {
            $xml = $dps->toXml($layout);
            (new SchemaValidator($layout))->validate($xml);

            $layout === Layout::Nt009
                ? $this->assertStringContainsString('<tpEmit>1</tpEmit><finNFSe>0</finNFSe>', $xml)
                : $this->assertStringNotContainsString('finNFSe', $xml);
        }
    }

    public function test_ibs_cbs_fields_move_between_layouts(): void
    {
        $ibs = ['IBSCBS' => [
            'finNFSe' => '0', 'indFinal' => '0', 'cIndOp' => '100301', 'indDest' => '0',
            'valores' => ['trib' => ['gIBSCBS' => ['CST' => '000', 'cClassTrib' => '000001']]],
        ]];

        $nt009 = Dps::make($this->dpsFields($ibs))->toXml(Layout::Nt009);
        (new SchemaValidator(Layout::Nt009))->validate($nt009);
        $this->assertStringContainsString('<trib><CST>000</CST><cClassTrib>000001</cClassTrib></trib>', $nt009);
        $this->assertSame(1, substr_count($nt009, '<finNFSe>'));

        // Escrito no formato novo, volta para o 1.01.
        $new = ['IBSCBS' => ['indFinal' => '0', 'cIndOp' => '100301', 'indDest' => '0', 'valores' => ['trib' => ['CST' => '000', 'cClassTrib' => '000001']]]];
        $v101 = Dps::make($this->dpsFields($new) + ['finNFSe' => '0'])->toXml(Layout::V101);
        (new SchemaValidator(Layout::V101))->validate($v101);
        $this->assertStringContainsString('<IBSCBS><finNFSe>0</finNFSe>', $v101);
        $this->assertStringContainsString('<gIBSCBS><CST>000</CST><cClassTrib>000001</cClassTrib></gIBSCBS>', $v101);
    }

    public function test_groups_without_equivalent_explain_what_to_use(): void
    {
        $this->expectException(InvalidDataException::class);
        $this->expectExceptionMessage('vAjusteBC');

        Dps::make($this->dpsFields(['valores' => ['vDedRed' => ['pDR' => 10]]]))->toXml(Layout::Nt009);
    }
}
