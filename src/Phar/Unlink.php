<?php

namespace Methylbro\Compiler\Phar;

class Unlink
{
    public function execute($phar, array $options)
    {
        $pharName = $options['distrib']['name'].'.phar';
        $pharFile = realpath($options['dest']).DIRECTORY_SEPARATOR.$pharName;

        if (file_exists($pharFile)) {
            unlink($pharFile);
        }

        return $phar;
    }
}
