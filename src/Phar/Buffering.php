<?php

namespace Millesime\Compiler\Phar;

use Schnittstabil\FinderByConfig\FinderByConfig;
use Methylbro\File\FileContents;
use Symfony\Component\Finder\SplFileInfo;

class Buffering
{
    private $filecontents;

    public function __construct(FileContents $filecontents)
    {
        $this->filecontents = $filecontents;
    }

    public function execute(\Phar $phar, array $options)
    {
        $finder = FinderByConfig::createFinder($options['distrib']['finder']);

        $phar->startBuffering();
        /** @var SplFileInfo $fileInfo */
        foreach ($finder as $fileInfo) {
            $file    = $fileInfo->getRelativePathname();
            $content = $fileInfo->getContents();

            if ($options['distrib']['autoexec'] && $file === $options['distrib']['stub']) {
                $content = str_replace('#!/usr/bin/env php'.PHP_EOL, null, $content);
            }

            if ($options) {
                $content = str_replace([
                    '@name@',
                    '@version@',
                    '@distrib@'
                ], [
                    $options['name'],
                    $options['version'],
                    $options['distrib']['name']
                ], $content);
            }

            $phar->addFromString($file, $content);
        }
        $phar->stopBuffering();

        return $phar;
    }
}
