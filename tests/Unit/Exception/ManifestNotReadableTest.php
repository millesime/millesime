<?php

namespace Millesime\Tests\Unit;

use \SplFileInfo;
use PHPUnit\Framework\TestCase;
use Millesime\Exception\ManifestNotReadable;

class ManifestNotReadableTest extends TestCase
{
    public function testConstruct()
    {
        $file = $this
            ->getMockBuilder(SplFileInfo::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $exception = new ManifestNotReadable($file);

        $this->assertEquals(
            ManifestNotReadable::MESSAGE,
            $exception->getMessage()
        );
        $this->assertEquals($file, $exception->jsonFile);
    }
}
