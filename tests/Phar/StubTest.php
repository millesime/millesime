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
        $factory = new Factory();
        $stub = new stub();
        $phar = $factory->execute(null, $options);
        $phar = $stub->execute($phar, $options);

        $this->assertEquals('', $phar->getStub());
    }
}
