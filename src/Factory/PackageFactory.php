<?php

namespace Millesime\Factory;

use \SplFileInfo;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Millesime\BuildPlan;
use Millesime\Package;
use Millesime\Event\PackageReady;

class PackageFactory
{
    private EventDispatcher $dispatcher;
    private LoggerInterface $logger;

    /**
     * @param EventDispatcher      $dispatcher
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        EventDispatcher $dispatcher,
        LoggerInterface $logger = null
    ) {
        $this->dispatcher = $dispatcher;
        $this->logger = $logger ?: new NullLogger;
    }

    public function __invoke(BuildPlan $buildPlan) : Package
    {
        $file = new SplFileInfo($buildPlan->getFileName());
        $needPublicKey = \Phar::OPENSSL===$buildPlan->getPackageInfo()->getSignature()->getAlgorithm();
        $package = new Package($file, $needPublicKey);

        $event = new PackageReady($package, $buildPlan);
        $this->dispatcher->dispatch($event, PackageReady::EVENT_NAME);

        $log = $package->getName();
        if ($package->needPublicKey()) {
            $log.= ', '.$package->getName().'.pubkey';
        }
        $this->logger->warning($log);

        return $package;
    }
}
