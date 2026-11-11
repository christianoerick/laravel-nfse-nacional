<?php

namespace ChristianoErick\NfseNacional\Tests;

use ChristianoErick\NfseNacional\Dps\Dps;
use ChristianoErick\NfseNacional\Xml\SchemaValidator;
use ChristianoErick\NfseNacional\Xml\XmlBuilder;
use ChristianoErick\NfseNacional\Xml\XmlSigner;

class SignerTest extends TestCase
{
    use BuildsNfse;

    public function test_signature_is_valid_and_xsd_compliant(): void
    {
        foreach (['sha1', 'sha256'] as $algorithm) {
            $dom = (new XmlBuilder)->build('DPS', Dps::make($this->dpsFields())->toArray());
            $xml = (string) (new XmlSigner($algorithm))->sign($dom, 'infDPS', $this->certificate())->saveXML();

            (new SchemaValidator)->validate($xml);
            $this->assertTrue(XmlSigner::verify($xml), "Assinatura {$algorithm} não conferiu.");
            $this->assertStringContainsString('<Reference URI="#DPS', $xml);
        }
    }

    public function test_tampered_xml_fails_verification(): void
    {
        $dom = (new XmlBuilder)->build('DPS', Dps::make($this->dpsFields())->toArray());
        $xml = (string) (new XmlSigner)->sign($dom, 'infDPS', $this->certificate())->saveXML();

        $this->assertFalse(XmlSigner::verify(str_replace('7500.00', '1.00', $xml)));
    }

    public function test_verifies_nested_signatures_of_an_nfse(): void
    {
        $xml = $this->makeNfseXml($this->certificate());

        (new SchemaValidator)->validate($xml);
        $this->assertTrue(XmlSigner::verify($xml));
    }
}
