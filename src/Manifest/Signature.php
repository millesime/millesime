<?php

namespace Millesime\Manifest;

class Signature
{
    const ALGORITHMS = [
        'MD5' => \Phar::MD5,
        'SHA1' => \Phar::SHA1,
        'SHA256' => \Phar::SHA256,
        'SHA512' => \Phar::SHA512,
        'OPENSSL' => \Phar::OPENSSL,
    ];

    private $algorithm;
    private $publicKey;
    private $privateKey;

    public function __construct($algorithm, $publicKey = null, $privateKey = null)
    {
        $this->algorithm = self::ALGORITHMS[$algorithm];
        $this->publicKey = $publicKey;
        $this->privateKey = $privateKey;
    }

    public function getAlgorithm()
    {
        return $this->algorithm;
    }

    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    public function getPublicKey()
    {
        return $this->publicKey;
    }
}
