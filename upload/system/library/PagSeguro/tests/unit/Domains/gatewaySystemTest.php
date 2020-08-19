<?php

use PHPUnit\Framework\TestCase;
use ValdeirPsr\PagSeguro\Domains\GatewaySystem;

class GatewaySystemTest extends TestCase
{
    /**
     * @test
     */
    public function newInstance()
    {
        $instance = new GatewaySystem();
        $this->assertInstanceOf(GatewaySystem::class, $instance);
    }
}
