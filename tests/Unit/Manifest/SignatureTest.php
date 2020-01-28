<?php

use PHPUnit\Framework\TestCase;

use Millesime\Manifest\Signature;

class SignatureTest extends TestCase
{
    public function testConstruct()
    {
        $signature = new Signature('MD5', 'public_key', 'private_key');

        $this->assertEquals(Phar::MD5, $signature->getAlgorithm());
        $this->assertEquals('public_key', $signature->getPublicKey());
        $this->assertEquals('private_key', $signature->getPrivateKey());
    }
}
