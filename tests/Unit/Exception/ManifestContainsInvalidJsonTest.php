<?php

use PHPUnit\Framework\TestCase;

use Millesime\Exception\ManifestContainsInvalidJson;

class ManifestContainsInvalidJsonTest extends TestCase
{
    public function testConstruct()
    {
        $invalidJson = '"lorem ipsum dolor""';
        $errorMsg = 'Control character error, possibly incorrectly encoded';

        $exception = new ManifestContainsInvalidJson($invalidJson, $errorMsg);

        $this->assertEquals(
            ManifestContainsInvalidJson::MESSAGE,
            $exception->getMessage()
        );
        $this->assertEquals($invalidJson, $exception->json);
        $this->assertEquals($errorMsg, $exception->reason);
    }
}