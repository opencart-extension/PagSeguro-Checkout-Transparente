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
    public function newInstanceWithValidArguments()
    {
        $street = 'Avenida Brasil';
        $number = '44878';
        $district = 'Campo Grande';
        $city = 'Rio de Janeiro';
        $state = 'RJ';
        $postalcode = '23078001';

        $instance = new Address(
            $street,
            $number,
            $district,
            $city,
            $state,
            $postalcode
        );

        $this->assertEquals(
            [
                $street,
                $number,
                $district,
                $city,
                $state,
                $postalcode
            ],
            [
                $instance->getStreet(),
                $instance->getNumber(),
                $instance->getDistrict(),
                $instance->getCity(),
                $instance->getState(),
                $instance->getPostalcode(),
            ]
        );
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
