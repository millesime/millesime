<?php

use PHPUnit\Framework\TestCase;

use Millesime\Manifest\PackageInfo;
use Millesime\Manifest\Signature;

class PackageInfoTest extends TestCase
{
    public function testConstruct()
    {
        $signature = $this
            ->getMockBuilder(Signature::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $packageInfo = new PackageInfo('name', new \stdClass, 'stub', true, $signature, ['script']);

        $this->assertEquals('name', $packageInfo->getName());
        $this->assertEquals('stub', $packageInfo->getStub());
        $this->assertTrue($packageInfo->isArchivedForTheWeb());
        $this->assertEquals($signature, $packageInfo->getSignature());
        $this->assertEquals(['script'], $packageInfo->getScripts());
    }
}
