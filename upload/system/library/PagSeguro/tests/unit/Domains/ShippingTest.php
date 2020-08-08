<?php

use PHPUnit\Framework\TestCase;
use ValdeirPsr\PagSeguro\Domains\Shipping;
use ValdeirPsr\PagSeguro\Constants\Shipping\Type as ShippingType;

class ShippingTest extends TestCase
{
    /**
     * @test
     */
    public function newInstance()
    {
        $instance = new Shipping;
        $this->assertInstanceOf(Shipping::class, $instance);
    }

    /**
     * @test
     */
    public function defineShippingTypeInvalidShouldGiveError()
    {
        $this->expectException(InvalidArgumentException::class);
        $instance = new Shipping;
        $instance->setType(4);
    }

    /**
     * @test
     */
    public function valueWithMoreThanTwoDecimalPlacesShouldGiveError()
    {
        $this->expectException(InvalidArgumentException::class);
        $instance = new Shipping;
        $instance->setCost(1307.1993);
    }
}
