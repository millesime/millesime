<?php

namespace Millesime\Compiler\Phar;

use Symfony\Component\Finder\Finder;
use Methylbro\File\FileContents;

class Buffering
{
    private $finder;
    private $filecontents;

    public function __construct(Finder $finder, FileContents $filecontents)
    {
        $this->finder = $finder;
        $this->filecontents = $filecontents;
    }

    public function execute(\Phar $phar, array $options)
    {
        $this->finder
            ->files()
            ->ignoreVCS(true)
            ->in($options['source'])
        ;

        $phar->startBuffering();
        foreach ($this->finder as $fileInfo) {
            $file = str_replace($options['source'], '', $fileInfo->getRelativePathname());
            $content = $this->filecontents->get($file);

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
