<?php

use PHPUnit\Framework\TestCase;
use ValdeirPsr\PagSeguro\Domains\Cart;
use ValdeirPsr\PagSeguro\Domains\CartItem;

class CartTest extends TestCase
{
    /**
     * @test
     */
    public function newInstance()
    {
        $instance = new Cart;
        $this->assertInstanceOf(Cart::class, $instance);
    }

    /**
     * @test
     */
    public function defineItemsWithValidArgumentsWithoutErrors()
    {
        $instance = new Cart([
            new CartItem(),
            new CartItem(),
            new CartItem(),
            new CartItem(),
            new CartItem(),
            new CartItem(),
            new CartItem()
        ]);

        $this->assertContainsOnlyInstancesOf(CartItem::class, $instance->getItems());
    }

    /**
     * @test
     */
    public function defineItemsWithInvalidArgumentsShouldGiveError()
    {
        $this->expectException(TypeError::class);
        $instance = new Cart([
            new CartItem(),
            new CartItem(),
            new CartItem(),
            new CartItem(),
            new CartItem(),
            new Cart(),
            new CartItem()
        ]);
    }
}
