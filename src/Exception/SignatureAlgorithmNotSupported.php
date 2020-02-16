<?php

namespace Millesime\Exception;

use \Phar;

class SignatureAlgorithmNotSupported extends \Exception
{
    private $algorithm;

    public function __construct($algorithm)
    {
        $this->algorithm = $algorithm;

        $message = 'Not supported signature algorithm.';
        $missingExtensionMessage = " %s extension has to be installed ";
        $missingExtensionMessage.= "to use %s signature algorithms.";

        switch ($algorithm) {
            case Phar::SHA256:
            case Phar::SHA512:
                if (!extension_loaded('hash')) {
                    $message.= sprintf(
                        $missingExtensionMessage,
                        "Hash", "SHA256 or SHA512"
                    );
                }
                break;
            case Phar::OPENSSL:
                if (!extension_loaded('openssl')) {
                    $message.= sprintf(
                        $missingExtensionMessage,
                        "OpenSSL", "OPENSSL"
                    );
                }
                break;
        }

        parent::__construct($message);
    }

    public function getAlgorithm()
    {
        return $this->algorithm;
    }
}
