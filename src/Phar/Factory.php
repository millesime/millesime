<?php

namespace Millesime\Compiler\Phar;

class Factory
{
    public function execute($phar, array $options)
    {
        $pharName = $options['distrib']['name'].'.phar';
        $pharFile = realpath($options['dest']).DIRECTORY_SEPARATOR.$pharName;

        $phar = new \Phar($pharFile);

        return $phar;
    }
}
