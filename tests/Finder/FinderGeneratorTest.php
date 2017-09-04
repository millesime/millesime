<?php

namespace Millesime\Finder\Tests;

use PHPUnit\Framework\TestCase;
use Millesime\Finder\FinderGenerator;

class FinderGeneratorTest extends TestCase
{
    public function testFinderFromConfig()
    {
        $config = [
            'source' => './',
            'distrib' => [
                'finder' => [
                    'in' => ['.'],
                ]
            ]
        ];

        $generator = new FinderGenerator();
        $finder = $generator->finderFromConfig($config);

        $this->assertInstanceOf(\Symfony\Component\Finder\Finder::class, $finder);
    }

    public function testFinderGeneric()
    {
        $generator = new FinderGenerator();
        $finderConfig = $generator->finderGeneric();

        $config = [
            'source' => './',
            'distrib' => [
                'finder' => $finderConfig,
            ]
        ];

        $finder = $generator->finderFromConfig($config);

        $this->assertInstanceOf(\Symfony\Component\Finder\Finder::class, $finder);
    }
}
