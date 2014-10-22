<?php

use SleepingOwl\Apist\Apist;

class ApistTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var TestApi
	 */
	protected $resource;

	protected function setUp()
	{
		parent::setUp();
		$this->resource = new TestApi;
	}

	/** @test */
	public function it_registers_new_resource()
	{
		$this->assertInstanceOf('\SleepingOwl\Apist\Apist', $this->resource);
	}

}
 