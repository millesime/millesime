<?php

namespace Millesime\Tests\Integration;

use Millesime\Millesime;

class MillesimeAPITest extends IntegrationTestCase
{
    public function testMillesimeAPI()
    {
        $path = $this->installTestProject();

        $millesime = new Millesime();
        $packages = $millesime($path);

        $this->assertTrue(file_exists($path.'/test-millesime-1.phar'));
        $this->assertTrue(file_exists($path.'/test-millesime-2.phar'));
    }
}