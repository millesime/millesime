<?php

namespace Millesime\Compiler\Finder;

use Schnittstabil\FinderByConfig\FinderByConfig;

class FinderGenerator extends FinderByConfig
{
    /**
     * @param $config
     * @return \Symfony\Component\Finder\Finder
     */
    public function finderFromConfig($config)
    {
    	$finder = $config['distrib']['finder'];

    	$dirs = $finder['in'];

    	$finder['in'] = [];

    	foreach ($dirs as $dir) {
	    	$finder['in'][] = realpath($config['source'].DIRECTORY_SEPARATOR.$dir);
    	}

        return parent::createFinder($finder);
    }
}
