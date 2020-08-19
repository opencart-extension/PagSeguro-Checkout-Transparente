<?php

use PHPUnit\Framework\TestCase;
use ValdeirPsr\PagSeguro\Domains\PaymentMethod\Boleto;

class BoletoTest extends TestCase
{
    /**
     * @test
     */
    public function newInstance()
    {
        $instance = new Boleto();
        $this->assertInstanceOf(Boleto::class, $instance);
    }

    /**
     * @test
     */
    public function getMethodShouldReturnBoleto()
    {
        $instance = new Boleto();
        $this->assertEquals('boleto', $instance->getMethod());
    }
}
