<?php

namespace Millesime\Manifest;

use Symfony\Component\Finder\Finder;
use Schnittstabil\FinderByConfig\FinderByConfig;

class PackageInfo
{
    private string $name;
    private \stdClass $finderConfig;
    private string $stub;
    private bool $webBased;
    private Signature $signature;
    private array $scripts;

    public function __construct(
        string $name,
        \stdClass $finderConfig,
        string $stub,
        bool $webBased,
        Signature $signature,
        array $scripts
    ) {
    	$this->name = $name;
    	$this->finderConfig = $finderConfig;
    	$this->stub = $stub;
    	$this->webBased = $webBased;
    	$this->signature = $signature;
    	$this->scripts = $scripts;
    }

    public function getName() : string
    {
    	return $this->name;
    }

    public function getFinder(string $workingDirectory) : Finder
    {
    	$config = $this->finderConfig;

        $in = [];
        foreach ($config->in as $dir) {
            $in[] = $workingDirectory.DIRECTORY_SEPARATOR.$dir;
        }
        $config->in = $in;

    	return FinderByConfig::createFinder($config);
    }

    public function getStub() : string
    {
    	return $this->stub;
    }

    public function isArchivedForTheWeb() : bool
    {
    	return $this->webBased;
    }

    public function getSignature() : Signature
    {
    	return $this->signature;
    }

    /**
     * @return string[]
     */
    public function getScripts() : Iterable
    {
    	return $this->scripts;
    }
}