<?php

use PHPUnit\Framework\TestCase;
use ValdeirPsr\PagSeguro\Domains\PaymentMethod\AbstractPaymentMethod;
use ValdeirPsr\PagSeguro\Domains\PaymentMethod\CreditCard;

class CreditCardTest extends TestCase
{
    /**
     * @test
     */
    public function newInstance()
    {
        $instance = new CreditCard();
        $this->assertInstanceOf(CreditCard::class, $instance);
        $this->assertInstanceOf(AbstractPaymentMethod::class, $instance);
    }

    /**
     * @test
     */
    public function getMethodShouldReturnCreditCard()
    {
        $instance = new CreditCard();
        $this->assertEquals('creditcard', $instance->getMethod());
    }
}
