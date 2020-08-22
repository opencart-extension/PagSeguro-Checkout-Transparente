<?php

use PHPUnit\Framework\TestCase;
use ValdeirPsr\PagSeguro\Domains\Address;
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

    /**
     * @test
     */
    public function populateFromXml()
    {
        $xml = '
        <shipping>
            <addressRequired>true</addressRequired>
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
            <type>3</type>
            <cost>1.00</cost>
        </shipping>
        ';

        $instance = Shipping::fromXml($xml);

        $this->assertEquals(true, $instance->getAddressRequired());
        $this->assertEquals(3, $instance->getType());
        $this->assertEquals(1.00, $instance->getCost());

        $address = $instance->getAddress();

        $this->assertEquals('Av. Brigadeiro Faria Lima', $address->getStreet());
        $this->assertEquals('1384', $address->getNumber());
        $this->assertEquals('1 andar', $address->getComplement());
        $this->assertEquals('Jardim Paulistano', $address->getDistrict());
        $this->assertEquals('Sao Paulo', $address->getCity());
        $this->assertEquals('SP', $address->getState());
        $this->assertEquals('BRA', $address->getCountry());
        $this->assertEquals('01452002', $address->getPostalCode());
    }

    /**
     * @test
     */
    public function populateToXml()
    {
        $xml = '
        <shipping>
            <type>3</type>
            <cost>0.00</cost>
            <addressRequired>true</addressRequired>
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
        </shipping>
        ';

        $address = new Address();

        $address->setStreet('Av. Brigadeiro Faria Lima');
        $address->setNumber('1384');
        $address->setComplement('1 andar');
        $address->setDistrict('Jardim Paulistano');
        $address->setCity('Sao Paulo');
        $address->setState('SP');
        $address->setPostalcode('01452002');

        $instance = new Shipping();
        $instance->setAddressRequired(true);
        $instance->setType(3);
        $instance->setCost(0);
        $instance->setAddress($address);

        $this->assertXmlStringEqualsXmlString($xml, $instance->toXml());
    }
}
