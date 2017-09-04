<?php

namespace Millesime\Phar;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Millesime\Compilation\Step;
use Millesime\Finder\FinderGenerator;

class Buffering implements Step
{
    /**
     * @var FinderGenerator
     */
    private $finderGenerator;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param FinderGenerator $finderGenerator
     * @param LoggerInterface $logger
     */
    public function __construct(FinderGenerator $finderGenerator, LoggerInterface $logger = null)
    {
        $this->finderGenerator = $finderGenerator;
        $this->logger = $logger ?: new NullLogger();
    }

    /**
     * @param \Phar $phar
     * @param array $options
     * @return \Phar
     */
    public function execute(\Phar $phar = null, array $options = array())
    {
        return $this->buffer(
            $this->finderGenerator->finderFromConfig($options),
            $phar,
            $options
        );
    }

    /**
     * @param \IteratorAggregate $finder
     * @param \Phar $phar
     * @param array $options
     * @return \Phar
     */
    private function buffer($finder, $phar, $options)
    {
        $phar->startBuffering();
        foreach ($finder as $fileInfo) {
            $this->bufferFile($fileInfo, $phar, $options);
        }
        $phar->stopBuffering();

        return $phar;
    }

    /**
     * @param \SPLFileInfo $fileInfo
     * @param \Phar $phar
     * @param array $options
     */
    private function bufferFile($fileInfo, $phar, $options)
    {
        $file = $fileInfo->getRelativePathname();
        $content = $fileInfo->getContents();

        if ($options['distrib']['autoexec'] && $file === $options['distrib']['stub']) {
            $content = str_replace('#!/usr/bin/env php'.PHP_EOL, null, $content);
        }

        $content = str_replace([
            '@name@',
            '@version@',
            '@distrib@'
        ], [
            $options['name'],
            $options['version'],
            $options['distrib']['name']
        ], $content);

        $phar->addFromString($file, $content);

        $this->logger->debug($file);
    }
}
