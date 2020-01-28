<?php

use PHPUnit\Framework\TestCase;

use Millesime\BuildPlan;
use Millesime\Event\CreatedPhar;
use Millesime\Factory\PharFactory;
use Symfony\Component\EventDispatcher\EventDispatcher;

class PharFactoryTest extends TestCase
{
    public function testConstruct()
    {
        $buildPlan = $this
            ->getMockBuilder(BuildPlan::class)
            ->disableOriginalConstructor()
            ->setMethods(['getFilename'])
            ->getMock()
        ;
        $buildPlan
            ->expects($this->any())
            ->method('getFilename')
            ->willReturn('myphar.phar')
        ;

        $dispatcher = $this
            ->getMockBuilder(EventDispatcher::class)
            ->disableOriginalConstructor()
            ->setMethods(['dispatch'])
            ->getMock()
        ;
        $dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(CreatedPhar::class), CreatedPhar::EVENT_NAME)
        ;
    
        $pharFactory = new PharFactory($dispatcher);
        $phar = $pharFactory($buildPlan);

        $this->assertInstanceOf(Phar::class, $phar);
    }

    public function testConstructWithExistingPhar()
    {
        $buildPlan = $this
            ->getMockBuilder(BuildPlan::class)
            ->disableOriginalConstructor()
            ->setMethods(['getFilename'])
            ->getMock()
        ;
        $buildPlan
            ->expects($this->any())
            ->method('getFilename')
            ->willReturn('myphar.phar')
        ;

        $dispatcher = $this
            ->getMockBuilder(EventDispatcher::class)
            ->disableOriginalConstructor()
            ->setMethods(['dispatch'])
            ->getMock()
        ;
        $dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(CreatedPhar::class), CreatedPhar::EVENT_NAME)
        ;
    
        $checkExistingPharMethod = $this
            ->getMockBuilder(\stdClass::class)
            ->setMethods(['__invoke'])
            ->getMock()
        ;
        $checkExistingPharMethod
            ->expects($this->once())
            ->method('__invoke')
            ->with('myphar.phar')
            ->willReturn(true)
        ;
        $deleteExistingPharMethod = $this
            ->getMockBuilder(\stdClass::class)
            ->setMethods(['__invoke'])
            ->getMock()
        ;
        $deleteExistingPharMethod
            ->expects($this->once())
            ->method('__invoke')
            ->with('myphar.phar')
            ->willReturn(true)
        ;

        $pharFactory = new PharFactory($dispatcher, null, $checkExistingPharMethod, $deleteExistingPharMethod);
        $phar = $pharFactory($buildPlan);

        $this->assertInstanceOf(Phar::class, $phar);
    }
}