<?php

namespace Millesime\Compiler\Phar\Tests;

use Millesime\Compiler\Finder\FinderGenerator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use Millesime\Compiler\Phar\Buffering;
use Methylbro\File\FileContents;

class BufferingTest extends TestCase
{
    public function testBuffering()
    {
        $options = [
            'name' => 'test',
            'version' => 'test',
            'source' => 'somewhere/',
            'distrib' => [
                'name' => 'test',
                'autoexec' => true,
                'stub' => 'foobar',
                'finder' => [
                    'in' => 'somewhere/'
                ]
            ]
        ];

        $filecontent = $this
            ->getMockBuilder(FileContents::class)
            ->getMock()
        ;

        $finder = $this
            ->getMockBuilder(Finder::class)
            ->setMethods(['files', 'ignoreVCS', 'in', 'getIterator'])
            ->getMock()
        ;

        $file = $this
            ->getMockBuilder(\SplFileInfo::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRelativePathname', 'getContents'])
            ->getMock()
        ;

        $file->method('getRelativePathname')->willReturn($options['source'].'foobar');
        $file->method('getContents')->willReturn('bar');

        $iterator = new \ArrayIterator();
        $iterator->append($file);

        $finder->method('files')->willReturn($finder);
        $finder->method('ignoreVCS')->willReturn($finder);
        $finder->method('in')->with($this->equalTo($options['distrib']['finder']['in']))->willReturn($finder);
        $finder->method('getIterator')->willReturn($iterator);

        $finderGenerator = $this
            ->getMockBuilder(FinderGenerator::class)
            ->setMethods(['finderFromConfig'])
            ->getMock()
        ;

        $finderGenerator
            ->method('finderFromConfig')
            ->with($this->equalTo($options))
            ->willReturn($finder)
        ;

        $phar = $this
            ->getMockBuilder('Phar')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $phar->expects($this->once())->method('addFromString')->with($this->equalTo('somewhere/foobar'));

        $buffering = new Buffering($finderGenerator, $filecontent);
        $buffering->execute($phar, $options);
    }
}
