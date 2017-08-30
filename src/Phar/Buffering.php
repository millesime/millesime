<?php

namespace Millesime\Compiler\Phar;

use Millesime\Compiler\Finder\FinderGenerator;
use Symfony\Component\Finder\SplFileInfo;

class Buffering
{
    private $finderGenerator;

    public function __construct(FinderGenerator $finderGenerator)
    {
        $this->finderGenerator = $finderGenerator;
    }

    public function execute(\Phar $phar, array $options)
    {
        $finder = $this->finderGenerator->finderFromConfig($options);

        $phar->startBuffering();
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
