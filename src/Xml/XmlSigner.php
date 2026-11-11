<?php

namespace ChristianoErick\NfseNacional\Xml;

use ChristianoErick\NfseNacional\Certificate\Certificate;
use ChristianoErick\NfseNacional\Exceptions\InvalidDataException;
use DOMDocument;
use DOMElement;
use DOMXPath;

/**
 * Assinatura XMLDSig enveloped do leiaute nacional: referencia o elemento
 * com Id (infDPS, infPedReg), C14N inclusiva, e a Signature fica como
 * irmã dele, no fim da raiz. RSA-SHA1 (manual) ou RSA-SHA256.
 */
final class XmlSigner
{
    public const DSIG = 'http://www.w3.org/2000/09/xmldsig#';

    private const ALGORITHMS = [
        'sha1' => [
            'signature' => 'http://www.w3.org/2000/09/xmldsig#rsa-sha1',
            'digest' => 'http://www.w3.org/2000/09/xmldsig#sha1',
            'openssl' => OPENSSL_ALGO_SHA1,
        ],
        'sha256' => [
            'signature' => 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256',
            'digest' => 'http://www.w3.org/2001/04/xmlenc#sha256',
            'openssl' => OPENSSL_ALGO_SHA256,
        ],
    ];

    private const C14N = 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315';

    private const ENVELOPED = 'http://www.w3.org/2000/09/xmldsig#enveloped-signature';

    public function __construct(private readonly string $algorithm = 'sha1')
    {
        if (! isset(self::ALGORITHMS[$algorithm])) {
            throw new InvalidDataException('Algoritmo de assinatura deve ser sha1 ou sha256.');
        }
    }

    /**
     * @param  string  $tag  elemento assinado (com atributo Id): infDPS, infPedReg
     */
    public function sign(DOMDocument $dom, string $tag, Certificate $certificate): DOMDocument
    {
        $target = $dom->getElementsByTagNameNS(SchemaMap::NAMESPACE, $tag)->item(0);

        if (! $target instanceof DOMElement || ! $target->hasAttribute('Id')) {
            throw new InvalidDataException("Elemento {$tag} com atributo Id não encontrado.");
        }

        $root = $dom->documentElement;

        foreach ($root->childNodes as $child) {
            if ($child instanceof DOMElement && $child->localName === 'Signature') {
                $root->removeChild($child);
            }
        }

        $algorithm = self::ALGORITHMS[$this->algorithm];
        $digest = base64_encode(hash($this->algorithm, $target->C14N(false, false), true));

        $signature = $dom->createElementNS(self::DSIG, 'Signature');
        $root->appendChild($signature);

        $signedInfo = $this->append($dom, $signature, 'SignedInfo');
        $this->append($dom, $signedInfo, 'CanonicalizationMethod')->setAttribute('Algorithm', self::C14N);
        $this->append($dom, $signedInfo, 'SignatureMethod')->setAttribute('Algorithm', $algorithm['signature']);

        $reference = $this->append($dom, $signedInfo, 'Reference');
        $reference->setAttribute('URI', '#'.$target->getAttribute('Id'));
        $transforms = $this->append($dom, $reference, 'Transforms');
        $this->append($dom, $transforms, 'Transform')->setAttribute('Algorithm', self::ENVELOPED);
        $this->append($dom, $transforms, 'Transform')->setAttribute('Algorithm', self::C14N);
        $this->append($dom, $reference, 'DigestMethod')->setAttribute('Algorithm', $algorithm['digest']);
        $this->append($dom, $reference, 'DigestValue', $digest);

        if (! openssl_sign($signedInfo->C14N(false, false), $signatureValue, $certificate->privateKey(), $algorithm['openssl'])) {
            throw new InvalidDataException('Não foi possível assinar com a chave do certificado.');
        }

        $this->append($dom, $signature, 'SignatureValue', base64_encode($signatureValue));
        $keyInfo = $this->append($dom, $signature, 'KeyInfo');
        $x509 = $this->append($dom, $keyInfo, 'X509Data');
        $this->append($dom, $x509, 'X509Certificate', $certificate->certificateBase64());

        return $dom;
    }

    /**
     * Confere digest e assinatura de um XML assinado (o nosso ou o devolvido
     * pelo Sefin, que assina a NFS-e em SHA-256 com exc-c14n).
     */
    public static function verify(string|DOMDocument $xml): bool
    {
        $dom = is_string($xml) ? XmlReader::load($xml) : $xml;
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('ds', self::DSIG);

        $signature = $xpath->query('/*/ds:Signature')->item(0);

        if (! $signature instanceof DOMElement) {
            return false;
        }

        $signedInfo = $xpath->query('ds:SignedInfo', $signature)->item(0);
        $reference = $xpath->query('ds:SignedInfo/ds:Reference', $signature)->item(0);
        $certificate = trim((string) $xpath->evaluate('string(ds:KeyInfo/ds:X509Data/ds:X509Certificate)', $signature));

        if (! $signedInfo instanceof DOMElement || ! $reference instanceof DOMElement || $certificate === '') {
            return false;
        }

        $id = ltrim($reference->getAttribute('URI'), '#');
        $target = $xpath->query("//*[@Id='{$id}']")->item(0);

        if (! $target instanceof DOMElement) {
            return false;
        }

        $digestAlgorithm = (string) $xpath->evaluate('string(ds:DigestMethod/@Algorithm)', $reference);
        $hash = str_contains($digestAlgorithm, 'sha256') ? 'sha256' : 'sha1';

        $canonical = self::canonicalize($target, (string) $xpath->evaluate('string(ds:Transforms/ds:Transform[last()]/@Algorithm)', $reference), $signature);
        $expectedDigest = trim((string) $xpath->evaluate('string(ds:DigestValue)', $reference));

        if (! hash_equals($expectedDigest, base64_encode(hash($hash, $canonical, true)))) {
            return false;
        }

        $signatureAlgorithm = (string) $xpath->evaluate('string(ds:SignatureMethod/@Algorithm)', $signedInfo);
        $c14n = (string) $xpath->evaluate('string(ds:CanonicalizationMethod/@Algorithm)', $signedInfo);
        $value = base64_decode(trim((string) $xpath->evaluate('string(ds:SignatureValue)', $signature)));
        $pem = "-----BEGIN CERTIFICATE-----\n".chunk_split(preg_replace('/\s+/', '', $certificate), 64, "\n")."-----END CERTIFICATE-----\n";

        return openssl_verify(
            self::canonicalize($signedInfo, $c14n, null),
            $value,
            $pem,
            str_contains($signatureAlgorithm, 'sha256') ? OPENSSL_ALGO_SHA256 : OPENSSL_ALGO_SHA1,
        ) === 1;
    }

    /**
     * C14N no próprio documento (mantém os namespaces herdados). A
     * transformação enveloped tira só a assinatura que está sendo conferida,
     * não as de documentos aninhados (a DPS assinada dentro da NFS-e).
     */
    private static function canonicalize(DOMElement $element, string $algorithm, ?DOMElement $signature): string
    {
        $exclusive = str_contains($algorithm, 'exc-c14n');
        $comments = str_ends_with($algorithm, 'WithComments');

        $inside = $signature !== null && self::isDescendant($signature, $element);
        $parent = $inside ? $signature->parentNode : null;
        $next = $inside ? $signature->nextSibling : null;

        if ($inside) {
            $parent->removeChild($signature);
        }

        $canonical = $element->C14N($exclusive, $comments);

        if ($inside) {
            $parent->insertBefore($signature, $next);
        }

        return $canonical;
    }

    private static function isDescendant(\DOMNode $node, DOMElement $ancestor): bool
    {
        for ($current = $node->parentNode; $current !== null; $current = $current->parentNode) {
            if ($current->isSameNode($ancestor)) {
                return true;
            }
        }

        return false;
    }

    private function append(DOMDocument $dom, DOMElement $parent, string $name, ?string $value = null): DOMElement
    {
        $element = $dom->createElementNS(self::DSIG, $name);

        if ($value !== null) {
            $element->appendChild($dom->createTextNode($value));
        }

        $parent->appendChild($element);

        return $element;
    }
}
