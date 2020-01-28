<?php

use PHPUnit\Framework\TestCase;

use Millesime\Manifest\Manifest;

class ManifestTest extends TestCase
{
    public function testConstruct()
    {
    	$date = $this
    		->getMockBuilder(\DateTime::class)
            ->disableOriginalConstructor()
            ->getMock()
    	;
    	$packagesInfo = [];

    	$manifest = new Manifest('project_name', 'version', $date, $packagesInfo);

        $this->assertEquals('project_name', $manifest->getProjectName());
        $this->assertEquals('version', $manifest->getVersion());
        $this->assertEquals($date, $manifest->getDate());
        $this->assertEquals($packagesInfo, $manifest->getPackagesInfo());
    }
}
