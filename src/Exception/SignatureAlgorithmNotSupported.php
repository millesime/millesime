<?php

namespace Millesime\Exception;

use \Phar;

class SignatureAlgorithmNotSupported extends \Exception
{
    private $algorithm;

    public function __construct($algorithm)
    {
        $this->algorithm = $algorithm;

        $message = '';

        switch ($algorithm) {
            case Phar::SHA256:
            case Phar::SHA512:
                extension_loaded('hash');
                break;
            case Phar::OPENSSL:
                extension_loaded('openssl');
                break;
        }

        parent::__construct($message);
    }
}
