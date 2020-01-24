<?php

namespace Millesime\Build;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Finder\Finder;
use Millesime\Event\CreatedPhar;

class Stub
{
    private Finder $finder;
    private LoggerInterface $logger;

    /**
     * @param Finder          $finder
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        Finder $finder,
        LoggerInterface $logger = null
    ) {
        $this->finder = $finder;
        $this->logger = $logger ?: new NullLogger;
    }

    public function __invoke(CreatedPhar $event)
    {
        $buildPlan = $event->getBuildPlan();
    	$phar = $event->getPhar();


        $name = $buildPlan
            ->getPackageInfo()
            ->isArchivedForTheWeb()
             ? 'web-stub-template.php' 
             : 'cli-stub-template.php'
        ;
        $files = $this
            ->finder
            ->files()
            ->name($name)
            ->in([__DIR__.'/../../templates'])
        ;
        /* @var Symfony\Component\Finder\SplFileInfo $file */
        $file = iterator_to_array($files, false)[0];
        if (!$file->isReadable()) {
            throw new \Exception();
        }

        $stub = str_replace(
            [
                '{release.date}',
                '{release.version}',
                '{package.name}',
                '{package.stub}',
            ], 
            [
                $buildPlan->getRelease()->getManifest()->getDate()->format(\DateTime::RFC3339_EXTENDED),
                $buildPlan->getRelease()->getManifest()->getVersion(),
                $buildPlan->getPackageInfo()->getName(),
                $buildPlan->getPackageInfo()->getStub(),
            ], 
            $file->getContents()
        );


        $phar->setStub($stub);
    }
}