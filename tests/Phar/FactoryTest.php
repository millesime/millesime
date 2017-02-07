<?php

namespace Millesime\Compiler\Phar\Tests;

use PHPUnit\Framework\TestCase;
use Millesime\Compiler\Phar\Factory;

class FactoryTest extends TestCase
{
    public function testFactory()
    {
        $options = [
            'dest' => '.',
            'distrib' => [
                'name' => 'test',
            ]
        ];

        $factory = new Factory();
        $phar = $factory->execute(null, $options);

        $this->assertInstanceOf(\Phar::class, $phar);
    }
}
