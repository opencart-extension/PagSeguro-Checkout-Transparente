<?php

use PHPUnit\Framework\TestCase;
use ValdeirPsr\PagSeguro\Domains\PaymentMethod\Boleto;
use ValdeirPsr\PagSeguro\Domains\PaymentMethod\AbstractPaymentMethod;

class BoletoTest extends TestCase
{
    /**
     * @test
     */
    public function newInstance()
    {
        $instance = new Boleto();
        $this->assertInstanceOf(Boleto::class, $instance);
        $this->assertInstanceOf(AbstractPaymentMethod::class, $instance);
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
