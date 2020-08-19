<?php

use PHPUnit\Framework\TestCase;
use ValdeirPsr\PagSeguro\Domains\User\Factory;
use ValdeirPsr\PagSeguro\Domains\User\Sender;
use ValdeirPsr\PagSeguro\Domains\User\Holder;

class FactoryMethodTest extends TestCase
{
    /**
     * @test
     */
    public function createSenderWithoutArguments()
    {
        $instance = Factory::sender();
        $this->assertInstanceOf(Sender::class, $instance);
    }

    /**
     * @test
     */
    public function createSenderWithArguments()
    {
        $instance = Factory::sender('Valdeir Psr', 'contato@valdeir.dev', null, null, '07031808');

        $this->assertEquals('Valdeir Psr', $instance->getName());
        $this->assertEquals('contato@valdeir.dev', $instance->getEmail());
        $this->assertEquals('07031808', $instance->getHash());
    }

    /**
     * @test
     */
    public function createHolderWithoutArguments()
    {
        $instance = Factory::Holder();
        $this->assertInstanceOf(Holder::class, $instance);
    }

    /**
     * @test
     */
    public function createHolderWithArguments()
    {
        $instance = Factory::Holder('Valdeir Psr', 'contato@valdeir.dev');

        $this->assertEquals('Valdeir Psr', $instance->getName());
        $this->assertEquals('contato@valdeir.dev', $instance->getEmail());
    }
}
