<?php

namespace Millesime\Compilation\Tests;

use PHPUnit\Framework\TestCase;
use Millesime\Compilation\ProjectFactory;

class ProjectFactoryTest extends TestCase
{
    public function testCreate()
    {
        $source = __DIR__.'/../../';
        $destination = $source;
        $manifest = 'millesime.json';

        $factory = new ProjectFactory();
        $project = $factory->create($source, $destination, $manifest);

        $this->assertEquals($source, $project->getSource());
        $this->assertEquals($destination, $project->getDestination());
    }

    public function testCreateWithoutExistingManifest()
    {
        $source = __DIR__.'/../../';
        $destination = $source;
        $manifest = 'test.json';

        $this->expectException(\InvalidArgumentException::class);

        $factory = new ProjectFactory();
        $project = $factory->create($source, $destination, $manifest);
    }

    public function testCreateWithManifestError()
    {
        $source = __DIR__.'/../../';
        $destination = $source;
        $manifest = 'phpunit.xml.dist';

        $this->expectException(\DomainException::class);

        $factory = new ProjectFactory();
        $project = $factory->create($source, $destination, $manifest);
    }
}
