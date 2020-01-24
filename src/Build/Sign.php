<?php

namespace Millesime\Build;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Millesime\Event\CreatedPhar;
use \Phar;

class Sign
{
    private $passphrase;
    private LoggerInterface $logger;

    /**
     * @param string|null          $passphrase
     * @param LoggerInterface|null $logger
     */
    public function __construct($passphrase = null, LoggerInterface $logger = null) 
    {
        $this->passphrase = $passphrase;
        $this->logger = $logger ?: new NullLogger;
    }

    public function __invoke(CreatedPhar $event)
    {
        $buildPlan = $event->getBuildPlan();
    	$phar = $event->getPhar();

        $signature = $buildPlan->getPackageInfo()->getSignature();

        switch ($signature->getAlgorithm()) {
            case Phar::MD5:
            case Phar::SHA1:
                trigger_error('Non efficient signature algorithm.', E_USER_DEPRECATED);
            case Phar::SHA256:
            case Phar::SHA512:
                $phar->setSignatureAlgorithm($signature->getAlgorithm());
                break;
            case Phar::OPENSSL:
                $private_key = file_get_contents($signature->getPrivateKey());
                $private_key = is_null($this->passphrase) 
                    ? $private_key
                    : openssl_pkey_get_private(
                        $private_key,
                        $this->passphrase
                    )
                ;
                $phar->setSignatureAlgorithm(Phar::OPENSSL, $private_key);

                /**
                 * « need to generate a public/private key pair, and 
                 * use the private key to set the signature using 
                 * Phar::setSignatureAlgorithm(). »
                 * @see https://www.php.net/manual/en/phar.using.intro.php
                 */
                file_put_contents(
                    $buildPlan->getFileName().'.pubkey',
                    file_get_contents($signature->getPublicKey())
                );
                break;
            default:
                throw new \Exception('unknow algorithm');
                break;
        }
    }
}