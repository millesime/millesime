<?php

use PHPUnit\Framework\TestCase;

use Millesime\Package;

class PackageTest extends TestCase
{
    public function testGetname()
    {
        $file = $this
            ->getMockBuilder(\SplFileInfo::class)
            ->disableOriginalConstructor()
            ->setMethods(['getFilename'])
            ->getMock()
        ;
        $file
            ->expects($this->once())
            ->method('getFilename')
            ->willReturn('package.phar')
        ;

        $package = new Package($file);

        $this->assertEquals('package.phar', $package->getName());
    }

    public function testOpen()
    {
        $file = $this
            ->getMockBuilder(\SplFileInfo::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPathname'])
            ->getMock()
        ;
        $file
            ->expects($this->once())
            ->method('getPathname')
            ->willReturn('package.phar')
        ;

        $package = new Package($file);
        $phar = $package->open();

        $this->assertInstanceOf(\Phar::class, $phar);
    }
}