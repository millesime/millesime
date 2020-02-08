<?php

namespace Millesime\Build;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Millesime\BuildPlan;
use Millesime\Exception;
use Millesime\Event\CreatedPhar;
use \Phar;

class Sign
{
    const ALGORITHMS = [
        Phar::MD5 => 'MD5',
        Phar::SHA1 => 'SHA-1',
        Phar::SHA256 => 'SHA-256',
        Phar::SHA512 => 'SHA-512',
        Phar::OPENSSL => 'OpenSSL',
    ];

    /** @var string|null Private key passphrase */
    private $passphrase;
    /** @var Array System supported algorithms */
    private $algorithms;
    /** @var LoggerInterface */
    private LoggerInterface $logger;

    /**
     * @param string|null          $passphrase Private key passphrase
     * @param Array|null           $algorithms System supported algorithms
     * @param LoggerInterface|null $logger     Logger
     */
    public function __construct(
        string $passphrase = null,
        Array $algorithms = null,
        LoggerInterface $logger = null
    ) {
        $this->passphrase = $passphrase;
        $this->algorithms = $algorithms ?: Phar::getSupportedSignatures();
        $this->logger = $logger ?: new NullLogger;
    }

    /**
     * All Phar archives signed with OPENSSL must be joined with a .pubkey file
     * withe the same name to be used. 
     * This method returns the .pubkey file name for a BuildPlan.
     *
     * @param BuildPlan $buildPlan
     * @return string
     */
    public static function getPubkeyFileName(BuildPlan $buildPlan)
    {
        return sprintf('%s.pubkey', $buildPlan->getFileName());
    }

    /**
     * @param CreatedPhar $event
     * @throws Exception\SignatureAlgorithmNotSupported If signature algorithm is not supported.
     */
    public function __invoke(CreatedPhar $event)
    {
        $buildPlan = $event->getBuildPlan();
        $signature = $buildPlan->getPackageInfo()->getSignature();
        $algorithm = $signature->getAlgorithm();

        if (
            !array_key_exists($algorithm, self::ALGORITHMS)
         || !in_array(self::ALGORITHMS[$algorithm], $this->algorithms)
        ) {
            throw new Exception\SignatureAlgorithmNotSupported($algorithm);
        }

        switch ($algorithm) {

            /**
             * Signature with hash algorithms.
             *
             * You may not use MD5 & SHA1 algorithms:
             * @see http://merlot.usc.edu/csac-f06/papers/Wang05a.pdf
             * @see http://people.csail.mit.edu/yiqun/SHA1AttackProceedingVersion.pdf
             * 
             * SHA256 & SHA512 requires hash extension.
             */
            case Phar::MD5:
            case Phar::SHA1:
                $this->logger->notice(
                    'You should not use MD5 or SHA1. Algorithm could be break.'
                );
            case Phar::SHA256:
            case Phar::SHA512:
                $event
                    ->getPhar()
                    ->setSignatureAlgorithm($algorithm)
                ;
                break;

            /**
             * Signature with OpenSSL public/private key pair.
             * This is a true, asymmetric key signature.
             *
             * « need to generate a public/private key pair, and 
             * use the private key to set the signature using 
             * Phar::setSignatureAlgorithm(). »
             * @see https://www.php.net/manual/en/phar.using.intro.php
             *
             * requires openssl extension.
             */
            case Phar::OPENSSL:
                $private_key = file_get_contents($signature->getPrivateKey());
                $private_key = is_empty($this->passphrase) 
                    ? null
                    : openssl_pkey_get_private(
                        $private_key,
                        $this->passphrase
                    )
                ;
                $event
                    ->getPhar()
                    ->setSignatureAlgorithm(Phar::OPENSSL, $private_key)
                ;
                file_put_contents(
                    self::getPubkeyFileName($buildPlan),
                    file_get_contents($signature->getPublicKey())
                );
                break;

        } // endswitch
    }
}
