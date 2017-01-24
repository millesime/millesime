<?php

namespace Millesime\Compiler\Tests;

use PHPUnit\Framework\TestCase;
use Millesime\Compiler\Project;

class ProjectTest extends TestCase
{
    public function testDefaultProject()
    {
        $source = __DIR__;
        $project = new Project($source);

        $this->assertEquals($source, $project->getSource());
        $this->assertEquals($source, $project->getDestination());
    }

    public function testUnexistingManifest()
    {
        $this->expectException(\Exception::class);

        $project = Project::manifest(__DIR__, __DIR__, 'foobar.json');
    }

    public function testManifest()
    {
        $manifest = 'millesime.json';
        $source = __DIR__.'/../';
        $project = Project::manifest($source, null, $manifest);

        $config = $project->getConfig();

        $this->assertEquals('Millesime', $config['name']);
    }
}
