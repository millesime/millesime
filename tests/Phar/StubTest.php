<?php

namespace Millesime\Phar\Tests;

use PHPUnit\Framework\TestCase;
use Millesime\Phar\Stub;

class StubTest extends TestCase
{
	public function testSetStub()
	{
		$pharName = 'test.phar';
		$stubfilename = 'stubfilename';
		$options = [
			'distrib' => [
				'name' => 'test',
				'stub' => $stubfilename,
				'autoexec' => true,
			],
		];

		$phar = $this
			->getMockBuilder(\Phar::class)
            ->disableOriginalConstructor()
            ->setMethods(['setStub'])
			->getMock()
		;
		$phar
            ->expects($this->once())
            ->method('setStub')
            ->with($this->stringContains("require 'phar://{$pharName}/{$stubfilename}';"))
        ;

		$stub = new Stub();
		$stub->execute($phar, $options);
	}
}
