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
        return parent::createFinder($config);
    }
}
