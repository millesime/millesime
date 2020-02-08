<?php

use PHPUnit\Framework\TestCase;

use Millesime\Build\Sign;
use Millesime\BuildPlan;
use Millesime\Manifest\PackageInfo;
use Millesime\Manifest\Signature;
use Millesime\Event\CreatedPhar;
use Symfony\Component\Finder\Finder;

class SignTest extends TestCase
{
    private function getCreatedPhar($algorithm, $fail = false)
    {
        $signature = $this
            ->getMockBuilder(Signature::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAlgorithm'])
            ->getMock()
        ;
        $signature->expects($this->once())->method('getAlgorithm')->willReturn($algorithm);
        $packageInfo = $this
            ->getMockBuilder(PackageInfo::class)
            ->disableOriginalConstructor()
            ->setMethods(['getSignature'])
            ->getMock()
        ;
        $packageInfo->expects($this->once())->method('getSignature')->willReturn($signature);
        $buildPlan = $this
            ->getMockBuilder(BuildPlan::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPackageInfo'])
            ->getMock()
        ;
        $buildPlan->expects($this->once())->method('getPackageInfo')->willReturn($packageInfo);

        $phar = $this
            ->getMockBuilder(Phar::class)
            ->disableOriginalConstructor()
            ->setMethods(['setSignatureAlgorithm'])
            ->getMock()
        ;
        if (!$fail) {
            $phar
                ->expects($this->once())
                ->method('setSignatureAlgorithm')
                ->with($algorithm)
            ;
        }

        return new CreatedPhar($phar, $buildPlan);
    }

    public function testHashedSignatures()
    {
        $algorithms = [Phar::MD5, Phar::SHA1, Phar::SHA256, Phar::SHA512];

        foreach ($algorithms as $algorithm) {
            $sign = new Sign();
            $sign($this->getCreatedPhar($algorithm));
        }
    }

    public function testWithNotSupportedAlgorithm()
    {
        $sign = new Sign();
        $this->expectException(\Millesime\Exception\SignatureAlgorithmNotSupported::class);
        $sign($this->getCreatedPhar("xxx", true));
    }

    public function testGetPubkeyFileName()
    {
        $buildPlan = $this
            ->getMockBuilder(BuildPlan::class)
            ->disableOriginalConstructor()
            ->setMethods(['getFileName'])
            ->getMock()
        ;
        $buildPlan->expects($this->once())->method('getFileName')->willReturn('foobar');

        $this->assertEquals('foobar.pubkey', Sign::getPubkeyFileName($buildPlan));
    }
}
