<?php

namespace Millesime\Event;

use Millesime\Package;
use Millesime\BuildPlan;

class PackageReady
{
    const EVENT_NAME = 'millesime.package_ready';

    private Package $package;
    private BuildPlan $buildPlan;

    public function __construct(Package $package, BuildPlan $buildPlan)
    {
        $this->package = $package;
        $this->buildPlan = $buildPlan;
    }

    public function getPackage() : Package
    {
        return $this->package;
    }

    public function getBuildPlan() : BuildPlan
    {
        return $this->buildPlan;
    }
}
