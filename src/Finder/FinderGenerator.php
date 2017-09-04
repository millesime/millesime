<?php

namespace Millesime\Finder;

use Schnittstabil\FinderByConfig\FinderByConfig;

class FinderGenerator extends FinderByConfig
{
    /**
     * @param Array $config
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

    /**
     * @return array
     */
    public function finderGeneric()
    {
        return [
            'in' => ['.'],
            'name' => ["*.php"],
            'notName' => ["*Test.php"],
            'notPath' => ["#vendor/.*/.*/Tests#", "#vendor/.*/.*/tests#"],
            'exclude' => ["test", "tests", "doc", "Test", "Tests"],
            'ignoreDotFiles' => true,
            'ignoreVCS' => true,
            'followLinks' => false,
            'ignoreUnreadableDirs' => false,
        ];
    }
}
