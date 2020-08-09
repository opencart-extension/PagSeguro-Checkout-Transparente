<?php

use PHPUnit\Framework\TestCase;
use ValdeirPsr\PagSeguro\Domains\CartItem;

class CartItemTest extends TestCase
{
    /**
     * @test
     */
    public function newInstance()
    {
        $instance = new CartItem;
        $this->assertInstanceOf(CartItem::class, $instance);
    }

    /**
     * @test
     */
    public function valueWithMoreThanTwoDecimalPlacesShouldGiveError()
    {
        $this->expectException(InvalidArgumentException::class);
        $instance = new CartItem;
        $instance->setAmount(1307.1993);
    }
}
