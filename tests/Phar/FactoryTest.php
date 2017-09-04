<?php

namespace Millesime\Phar\Tests;

use PHPUnit\Framework\TestCase;
use Millesime\Phar\Factory;

class FactoryTest extends TestCase
{
    public function testFactory()
    {
        $options = ['filename' => 'test.phar'];

        $factory = new Factory();
        $phar = $factory->execute(null, $options);

        $this->assertInstanceOf(\Phar::class, $phar);
    }
}
