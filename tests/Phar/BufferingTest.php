<?php

namespace Millesime\Compiler\Phar\Tests;

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
                'stub' => 'foobar'
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
            ->setMethods(['getRelativePathname'])
            ->getMock()
        ;

        $file->method('getRelativePathname')->willReturn($options['source'].'foobar');

        $iterator = new \ArrayIterator();
        $iterator->append($file);

        $finder->method('files')->willReturn($finder);
        $finder->method('ignoreVCS')->willReturn($finder);
        $finder->method('in')->with($this->equalTo($options['source']))->willReturn($finder);
        $finder->method('getIterator')->willReturn($iterator);

        $phar = $this
            ->getMockBuilder('Phar')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $phar->expects($this->once())->method('addFromString')->with($this->equalTo('somewhere/foobar'));

        $buffering = new Buffering($finder, $filecontent);
        $buffering->execute($phar, $options);
    }
}
