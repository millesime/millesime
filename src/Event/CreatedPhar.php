<?php

namespace Millesime\Event;

use \Phar;
use Millesime\BuildPlan;

class CreatedPhar
{
    const EVENT_NAME = 'millesime.created_phar';

    private Phar $phar;
    private BuildPlan $buildPlan;

    public function __construct(Phar $phar, BuildPlan $buildPlan)
    {
        $this->phar = $phar;
        $this->buildPlan = $buildPlan;
    }

    public function getPhar() : Phar
    {
        return $this->phar;
    }

    public function getBuildPlan() : BuildPlan
    {
        return $this->buildPlan;
    }
}
