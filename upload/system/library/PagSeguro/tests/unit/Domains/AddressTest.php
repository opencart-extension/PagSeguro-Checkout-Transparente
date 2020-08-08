<?php

use PHPUnit\Framework\TestCase;
use ValdeirPsr\PagSeguro\Domains\Address;

class AddressTest extends TestCase
{
    /**
     * @test
     */
    public function newInstanceWithoutArguments()
    {
        $instance = new Address;
        $this->assertInstanceOf(Address::class, $instance);
    }

    /**
     * @test
     */
    public function checkOptionalComplement()
    {
        $instance = new Address;
        $instance->setComplement(null);
        $this->assertNull($instance->getComplement());
    }

    /**
     * @test
     */
    public function thePostalFieldShouldOnlyReturnNumbers()
    {
        $instance = new Address;
        $instance->setPostalCode('23.078-001');
        $this->assertEquals('23078001', $instance->getPostalCode());
    }
}
