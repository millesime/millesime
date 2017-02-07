<?php

namespace Millesime\Compiler\Phar\Tests;

use PHPUnit\Framework\TestCase;
use Millesime\Compiler\Phar\Factory;
use Millesime\Compiler\Phar\Stub;

class StubTest extends TestCase
{
    public function testStub()
    {
        $options = [
            'dest' => '.',
            'distrib' => [
                'name' => 'test',
                'stub' => '',
                'autoexec' => false,
            ]
        ];

        $stub = new Stub();

        $phar = $this
            ->getMockBuilder('Phar')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $phar = $stub->execute($phar, $options);

        $this->assertEquals('', $phar->getStub());
    }

    public function tearDown()
    {
        if (is_file('test.phar')) {
            unlink('test.phar');
        }

        parent::tearDown();
    }
}
