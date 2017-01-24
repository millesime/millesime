<?php

namespace Methylbro\Compiler\Phar\Tests;

use PHPUnit\Framework\TestCase;
use Methylbro\Compiler\Phar\Factory;
use Methylbro\Compiler\Phar\Stub;

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
