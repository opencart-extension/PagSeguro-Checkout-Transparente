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

    /**
     * @test
     */
    public function populateFromXml()
    {
        $xml = '
        <item>
            <id>1</id>
            <description>Descricao do item a ser vendido</description>
            <quantity>2</quantity>
            <amount>1.00</amount>
        </item>
        ';

        $instance = CartItem::fromXml($xml);
        
        $this->assertEquals('1', $instance->getId());
        $this->assertEquals('Descricao do item a ser vendido', $instance->getDescription());
        $this->assertEquals('2', $instance->getQuantity());
        $this->assertEquals('1.00', $instance->getAmount());

    }

    /**
     * @test
     */
    public function populateToXml()
    {
        $xml = '
        <item>
            <id>1</id>
            <description>Descricao do item a ser vendido</description>
            <amount>1.00</amount>
            <quantity>2</quantity>
        </item>
        ';

        $instance = new CartItem();

        $instance->setId(1);
        $instance->setDescription('Descricao do item a ser vendido');
        $instance->setQuantity(2);
        $instance->setAmount(1.00);

        $this->assertXmlStringEqualsXmlString($xml, $instance->toXml());
    }
}
