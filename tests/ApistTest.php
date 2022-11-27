<?php

namespace SleepingOwl\Apist\Tests;

use PHPUnit\Framework\TestCase;
use SleepingOwl\Apist\Apist;

/**
 * @internal
 *
 * @coversNothing
 */
class ApistTest extends TestCase
{
    /** @var TestApi */
    protected $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = new TestApi();
    }

    public function testItRegistersNewResource(): void
    {
        $this->assertInstanceOf(Apist::class, $this->resource);
    }
}
