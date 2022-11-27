<?php

namespace SleepingOwl\Apist\Tests;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

/**
 * @internal
 *
 * @coversNothing
 */
class ApistMethodTest extends TestCase
{
    /**
     * @var TestApi
     */
    protected $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = new TestApi();

        $response = $this->getMockBuilder(ResponseInterface::class)
            ->getMock()
        ;

        $response->expects($this->once())
            ->method('getBody')
            ->willReturn(file_get_contents(__DIR__.'/stub/index.html'))
        ;

        $client = $this->getMockBuilder(Client::class)
            ->getMock()
        ;

        $client->method('request')
            ->willReturn($response)
        ;

        $this->resource->setHttpClient($client);
    }

    public function testItParsesResultByBlueprint(): void
    {
        $result = $this->resource->index();

        $this->assertEquals('Моя лента', $result['title']);
        $this->assertEquals('http://tmtm.ru/', $result['copyright']);
        $this->assertCount(10, $result['posts']);
    }

    public function testItReturnsNullIfElementNotFound(): void
    {
        $result = $this->resource->elementNotFound();

        $this->assertEquals(['title' => null], $result);
    }

    public function testItParsesNonArrayBlueprint(): void
    {
        $result = $this->resource->nonArrayBlueprint();

        $this->assertEquals('Моя лента', $result);
    }
}
