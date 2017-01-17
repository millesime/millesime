<?php

namespace Methylbro\Compiler\Phar;

use Symfony\Component\Finder\Finder;

class Buffering
{
    public function __construct()
    {}

    public function execute($phar, array $options)
    {
        $finder = new Finder();
        $finder
            ->files()
            ->ignoreVCS(true)
            ->in($options['source'])
        ;

        $phar->startBuffering();
        foreach ($finder as $fileInfo) {
            $file = str_replace($options['source'], '', $fileInfo->getRelativePathname());
            $content = file_get_contents($file);

            if ($options['distrib']['autoexec'] && $file==$options['distrib']['stub']) {
                $content = str_replace('#!/usr/bin/env php'.PHP_EOL, null, $content);
            }

            if ($options) {
                $content = str_replace('@name@', $options['name'], $content);
                $content = str_replace('@version@', $options['version'], $content);
                $content = str_replace('@distrib@', $options['distrib']['name'], $content);
            }

            $phar->addFromString($fileInfo->getRelativePathname(), $content);
        }
        $phar->stopBuffering();

        return $phar;
    }
}
