<?php

use PHPUnit\Framework\TestCase;

use Millesime\Exception\ManifestNotFound;

class ManifestNotFoundTest extends TestCase
{
    public function testConstruct()
    {
        $filename = 'manifest.json';
        $path = '/path/to/project';

        $exception = new ManifestNotFound($filename, $path);

        $this->assertEquals(
            ManifestNotFound::MESSAGE,
            $exception->getMessage()
        );
        $this->assertEquals($filename, $exception->filename);
        $this->assertEquals($path, $exception->path);
    }
}