<?php

namespace ChristianoErick\NfseNacional\Tests;

use ChristianoErick\NfseNacional\Certificate\Certificate;
use ChristianoErick\NfseNacional\Dps\Dps;
use ChristianoErick\NfseNacional\Xml\SchemaMap;
use ChristianoErick\NfseNacional\Xml\XmlBuilder;
use ChristianoErick\NfseNacional\Xml\XmlSigner;
use DOMDocument;

/**
 * Simula a NFS-e que o Sefin devolve: infNFSe com os dados gerados, a DPS
 * assinada dentro, e a assinatura do "Sefin" (aqui, o certificado de teste).
 */
trait BuildsNfse
{
    protected function makeNfseXml(Certificate $certificate, ?Dps $dps = null, string $accessKey = '27043022211222333000181000000000000126113381019914'): string
    {
        $dps ??= Dps::make($this->dpsFields());

        $signedDps = new DOMDocument;
        $signedDps->loadXML((string) (new XmlSigner)->sign((new XmlBuilder)->build('DPS', $dps->toArray()), 'infDPS', $certificate)->saveXML());

        $nfse = (new XmlBuilder)->build('NFSe', [
            '@versao' => '1.01',
            'infNFSe' => [
                '@Id' => 'NFS'.$accessKey,
                'xLocEmi' => 'Maceió',
                'xLocPrestacao' => 'Maceió',
                'nNFSe' => '1',
                'cLocIncid' => self::CITY,
                'xLocIncid' => 'Maceió',
                'xTribNac' => 'Licenciamento ou cessão de direito de uso de programas de computação.',
                'verAplic' => 'EmissorWeb_1.6.0.0',
                'ambGer' => 2,
                'tpEmis' => 1,
                'procEmi' => 2,
                'cStat' => 100,
                'dhProc' => '2026-11-11T16:27:00-03:00',
                'nDFSe' => '928488',
                'emit' => [
                    'CNPJ' => self::PROVIDER_CNPJ,
                    'xNome' => 'EMPRESA TESTE LTDA',
                    'enderNac' => ['xLgr' => 'AVENIDA TESTE', 'nro' => '1', 'xBairro' => 'CENTRO', 'cMun' => self::CITY, 'UF' => 'AL', 'CEP' => '57000000'],
                ],
                'valores' => ['vLiq' => '7500.00'],
            ],
        ]);

        $infNfse = $nfse->getElementsByTagNameNS(SchemaMap::NAMESPACE, 'infNFSe')->item(0);
        $infNfse->appendChild($nfse->importNode($signedDps->documentElement, true));

        (new XmlSigner('sha256'))->sign($nfse, 'infNFSe', $certificate);

        return (string) $nfse->saveXML();
    }
}
