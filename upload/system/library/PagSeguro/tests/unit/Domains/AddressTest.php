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

    /**
     * @test
     */
    public function populateFromXml()
    {
        $xml = '
            <address>
                <street>Av. Brigadeiro Faria Lima</street>
                <number>1384</number>
                <complement>1 andar</complement>
                <district>Jardim Paulistano</district>
                <city>Sao Paulo</city>
                <state>SP</state>
                <country>BRA</country>
                <postalCode>01452002</postalCode>
            </address>
        ';

        $instance = Address::fromXml($xml);

        $this->assertEquals('Av. Brigadeiro Faria Lima', $instance->getStreet());
        $this->assertEquals('1384', $instance->getNumber());
        $this->assertEquals('1 andar', $instance->getComplement());
        $this->assertEquals('Jardim Paulistano', $instance->getDistrict());
        $this->assertEquals('Sao Paulo', $instance->getCity());
        $this->assertEquals('SP', $instance->getState());
        $this->assertEquals('BRA', $instance->getCountry());
        $this->assertEquals('01452002', $instance->getPostalcode());
    }

    /**
     * @test
     */
    public function populateToXml()
    {
        $xml = '
        <address>
            <street>Av. Brigadeiro Faria Lima</street>
            <number>1384</number>
            <complement>1 andar</complement>
            <district>Jardim Paulistano</district>
            <city>Sao Paulo</city>
            <state>SP</state>
            <country>BRA</country>
            <postalCode>01452002</postalCode>
        </address>
        ';

        $instance = new Address();

        $instance->setStreet('Av. Brigadeiro Faria Lima');
        $instance->setNumber('1384');
        $instance->setComplement('1 andar');
        $instance->setDistrict('Jardim Paulistano');
        $instance->setCity('Sao Paulo');
        $instance->setState('SP');
        $instance->setPostalcode('01452002');

        $this->assertXmlStringEqualsXmlString($xml, $instance->toXml());
    }
}
