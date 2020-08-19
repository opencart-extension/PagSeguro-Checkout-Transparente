<?php

use PHPUnit\Framework\TestCase;
use ValdeirPsr\PagSeguro\Domains\PaymentMethod\AbstractPaymentMethod;
use ValdeirPsr\PagSeguro\Domains\PaymentMethod\DebitCard;

class DebitCardTest extends TestCase
{
    /**
     * @test
     */
    public function newInstance()
    {
        $instance = new DebitCard();
        $this->assertInstanceOf(DebitCard::class, $instance);
        $this->assertInstanceOf(AbstractPaymentMethod::class, $instance);
    }

    /**
     * @test
     */
    public function getMethodShouldReturnEFT()
    {
        $instance = new DebitCard();
        $this->assertEquals('eft', $instance->getMethod());
    }
}
