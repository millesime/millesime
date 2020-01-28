<?php

use PHPUnit\Framework\TestCase;

use Millesime\BuildPlan;
use Millesime\Event\CreatedPhar;

class CreatedPharTest extends TestCase
{
    public function testConstruct()
    {
        $phar = $this
            ->getMockBuilder(Phar::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $buildPlan = $this
            ->getMockBuilder(BuildPlan::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $createdPhar = new CreatedPhar($phar, $buildPlan);

        $this->assertEquals($phar, $createdPhar->getPhar());
        $this->assertEquals($buildPlan, $createdPhar->getBuildPlan());
    }
}