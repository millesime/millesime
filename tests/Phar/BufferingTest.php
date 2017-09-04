<?php

namespace Millesime\Phar\Tests;

use PHPUnit\Framework\TestCase;
use Millesime\Phar\Buffering;
use Millesime\Finder\FinderGenerator;

class BufferingTest extends TestCase
{
    public function testBuffering()
    {
        $options = [
            'name' => '',
            'version' => '',
            'distrib' => [
                'name' => '',
                'autoexec' => true,
                'stub' => 'foobarfile',
            ],
        ];

        $file = $this
            ->getMockBuilder(\SplFileInfo::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRelativePathname', 'getContents'])
            ->getMock()
        ;
        $file
            ->expects($this->once())
            ->method('getRelativePathname')
            ->willReturn('foobarfile')
        ;
        $file
            ->expects($this->once())
            ->method('getContents')
            ->willReturn(
<<<PHP
#!/usr/bin/env php
<?php
echo "hello world";
PHP
            )
        ;

        $finder = $this
            ->getMockBuilder(FinderGenerator::class)
            ->setMethods(['finderFromConfig'])
            ->getMock()
        ;
        $finder
            ->expects($this->once())
            ->method('finderFromConfig')
            ->with($options)
            ->willReturn([$file])
        ;

        $phar = $this
            ->getMockBuilder(\Phar::class)
            ->disableOriginalConstructor()
            ->setMethods(['startBuffering', 'stopBuffering', 'addFromString'])
            ->getMock()
        ;
        $phar
            ->expects($this->once())
            ->method('startBuffering')
        ;
        $phar
            ->expects($this->once())
            ->method('stopBuffering')
        ;
        $phar
            ->expects($this->once())
            ->method('addFromString')
            ->with(
                "foobarfile",
<<<PHP
<?php
echo "hello world";
PHP
            )
        ;

        $buffering = new Buffering($finder);
        $phar = $buffering->execute($phar, $options);
    }
}
