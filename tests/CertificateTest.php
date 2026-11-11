<?php

namespace ChristianoErick\NfseNacional\Tests;

use ChristianoErick\NfseNacional\Certificate\Certificate;
use ChristianoErick\NfseNacional\Exceptions\CertificateException;

class CertificateTest extends TestCase
{
    public function test_reads_holder_document_and_validity(): void
    {
        $certificate = $this->certificate(days: 30);

        $this->assertSame('EMPRESA TESTE LTDA', $certificate->holderName());
        $this->assertSame(self::PROVIDER_CNPJ, $certificate->document());
        $this->assertTrue($certificate->isCompany());
        $this->assertFalse($certificate->isExpired());
        $this->assertContains($certificate->daysUntilExpiration(), [29, 30]);
        $this->assertSame(40, strlen($certificate->fingerprint()));
    }

    public function test_wrong_password_throws(): void
    {
        $this->expectException(CertificateException::class);

        Certificate::fromPfx($this->makePfx('right'), 'wrong');
    }

    public function test_reads_cpf_holder(): void
    {
        $certificate = Certificate::fromPfx($this->makePfx('x', 10, 'FULANO DE TAL:52998224725'), 'x');

        $this->assertSame('52998224725', $certificate->document());
        $this->assertFalse($certificate->isCompany());
    }

    public function test_exports_modern_pfx(): void
    {
        $certificate = $this->certificate();
        $again = Certificate::fromPfx($certificate->toPfx('new-pass'), 'new-pass');

        $this->assertSame($certificate->fingerprint(), $again->fingerprint());
    }

    public function test_reads_legacy_rc2_pfx_through_openssl_binary(): void
    {
        $dir = sys_get_temp_dir().'/nfse-legacy-'.uniqid();
        mkdir($dir);
        file_put_contents($dir.'/cert.pfx', $this->makePfx('legacy'));

        // Regrava com cifra antiga (RC2-40), como os A1 emitidos há alguns anos.
        exec(sprintf(
            'openssl pkcs12 -in %1$s/cert.pfx -passin pass:legacy -nodes -out %1$s/all.pem 2>/dev/null && openssl pkcs12 -export -legacy -in %1$s/all.pem -passout pass:legacy -out %1$s/legacy.pfx 2>/dev/null',
            escapeshellarg($dir),
        ), $output, $code);

        if ($code !== 0 || ! is_file($dir.'/legacy.pfx')) {
            $this->markTestSkipped('openssl sem -legacy neste ambiente.');
        }

        $certificate = Certificate::fromPath($dir.'/legacy.pfx', 'legacy');
        $this->assertSame(self::PROVIDER_CNPJ, $certificate->document());

        array_map('unlink', glob($dir.'/*'));
        rmdir($dir);
    }
}
