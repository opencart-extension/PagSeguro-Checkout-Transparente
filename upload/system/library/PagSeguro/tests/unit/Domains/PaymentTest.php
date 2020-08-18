<?php

use PHPUnit\Framework\TestCase;
use ValdeirPsr\PagSeguro\Domains\Payment;

class PaymentTest extends TestCase
{
    /**
     * @test
     */
    public function newInstance()
    {
        $instance = new Payment();
        $this->assertInstanceOf(Payment::class, $instance);
    }
}
