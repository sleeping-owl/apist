<?php

use SleepingOwl\Apist\Apist;

class ApistMethodTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var TestApi
	 */
	protected $resource;

	protected function setUp()
	{
		parent::setUp();
		$this->resource = new TestApi;

		$response = Mockery::mock();
		$response->shouldReceive('getBody')->andReturn(file_get_contents(__DIR__ . '/stub/index.html'));

		$client = Mockery::mock();
		$client->shouldReceive('createRequest')->andReturnUsing(function ($method, $url)
		{
			return $url;
		});
		$client->shouldReceive('send')->andReturn($response);

		$this->resource->setGuzzle($client);
	}

	/** @test */
	public function it_parses_result_by_blueprint()
	{
		$result = $this->resource->index();

		$this->assertEquals('Моя лента', $result['title']);
		$this->assertEquals('http://tmtm.ru/', $result['copyright']);
		$this->assertCount(10, $result['posts']);
	}

	/** @test */
	public function it_returns_null_if_element_not_found()
	{
		$result = $this->resource->element_not_found();

		$this->assertEquals(['title' => null], $result);
	}

	/** @test */
	public function it_parses_non_array_blueprint()
	{
		$result = $this->resource->non_array_blueprint();

		$this->assertEquals('Моя лента', $result);
	}

}
 