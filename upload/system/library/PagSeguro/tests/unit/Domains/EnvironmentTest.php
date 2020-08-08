<?php

use PHPUnit\Framework\TestCase;
use ValdeirPsr\PagSeguro\Domains\Environment;

class EnvironmentTest extends TestCase
{
    /**
     * @test
     */
    public function newInstanceSandbox()
    {
        $instance = Environment::sandbox('naval@sandbox.pagseguro.com.br', 'ABC123');
        $this->assertInstanceOf(Environment::class, $instance);
    }

    /**
     * @test
     */
    public function newInstanceProduction()
    {
        $instance = Environment::production('naval@pagseguro.com.br', 'ABC123');
        $this->assertInstanceOf(Environment::class, $instance);
    }

    /**
     * @test
     */
    public function newInstanceSandboxWithInvalidArgumentShouldGiveError()
    {
        $this->expectException(InvalidArgumentException::class);
        Environment::sandbox('@sandbox.pagseguro.com.br', 'ABC123');
    }

    /**
     * @test
     */
    public function testIsSandbox()
    {
        $instance = Environment::sandbox('naval@sandbox.pagseguro.com.br', 'ABC123');
        $this->assertTrue($instance->isSandbox());
    }
}
