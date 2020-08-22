<?php

use PHPUnit\Framework\TestCase;
use ValdeirPsr\PagSeguro\Domains\Payment;
use ValdeirPsr\PagSeguro\Domains\CartItem;
use ValdeirPsr\PagSeguro\Domains\User\Sender;
use ValdeirPsr\PagSeguro\Domains\Address;
use ValdeirPsr\PagSeguro\Domains\Shipping;
use ValdeirPsr\PagSeguro\Domains\Document;
use ValdeirPsr\PagSeguro\Domains\PaymentMethod\Boleto;

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

    /**
     * @test
     */
    public function populateFromXml()
    {
        $xml = '
        <payment>
            <mode>default</mode>
            <method>boleto</method>
            <sender>
                <name>Fulano Silva</name>
                <email>fulano.silva@sandbox.pagseguro.com.br</email>
                <phone>
                    <areaCode>11</areaCode>
                    <number>30380000</number>
                </phone>
                <documents>
                    <document>
                        <type>CPF</type>
                        <value>72962940005</value>
                    </document>
                </documents>
                <hash>{{hash_do_comprador}}</hash>
            </sender>
            <currency>BRL</currency>
            <notificationURL>https://sualoja.com.br/notificacao</notificationURL>
            <items>
                <item>
                    <id>1</id>
                    <description>Descricao do item a ser vendido</description>
                    <quantity>2</quantity>
                    <amount>1.00</amount>
                </item>
            </items>
            <extraAmount>0.00</extraAmount>
            <reference>R123456</reference>
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
                <cost>0.00</cost>
            </shipping>
        </payment>
        ';

        $instance = Payment::fromXml($xml);

        $this->assertEquals('default', $instance->getMode());
        $this->assertEquals('boleto', $instance->getPayment()->getMethod());
        $this->assertEquals('Fulano Silva', $instance->getSender()->getName());
        $this->assertEquals('BRL', $instance->getCurrency());
        $this->assertEquals('https://sualoja.com.br/notificacao', $instance->getNotificationUrl());
        $this->assertCount(1, $instance->getCartItems());
        $this->assertEquals(0.00, $instance->getExtraAmount());
        $this->assertEquals('R123456', $instance->getReference());
        $this->assertEquals('Av. Brigadeiro Faria Lima', $instance->getShipping()->getAddress()->getStreet());
    }

    /**
     * @test
     */
    public function populateToXml()
    {
        $xml = '
        <payment>
            <mode>default</mode>
            <method>boleto</method>
            <sender>
                <name>Fulano Silva</name>
                <email>fulano.silva@sandbox.pagseguro.com.br</email>
                <hash>{{hash_do_comprador}}</hash>
                <phone>
                    <areaCode>11</areaCode>
                    <number>30380000</number>
                </phone>
                <documents>
                    <document>
                        <type>CPF</type>
                        <value>72962940005</value>
                    </document>
                </documents>
            </sender>
            <currency>BRL</currency>
            <notificationURL>https://sualoja.com.br/notificacao</notificationURL>
            <items>
                <item>
                    <id>1</id>
                    <description>Descricao do item a ser vendido</description>
                    <amount>1.00</amount>
                    <quantity>2</quantity>
                </item>
            </items>
            <extraAmount>100.50</extraAmount>
            <reference>R123456</reference>
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
        </payment>
        ';

        $cartItem = new CartItem();

        $cartItem->setId(1);
        $cartItem->setDescription('Descricao do item a ser vendido');
        $cartItem->setQuantity(2);
        $cartItem->setAmount(1.00);

        $address = new Address();

        $address->setStreet('Av. Brigadeiro Faria Lima');
        $address->setNumber('1384');
        $address->setComplement('1 andar');
        $address->setDistrict('Jardim Paulistano');
        $address->setCity('Sao Paulo');
        $address->setState('SP');
        $address->setPostalcode('01452002');

        $shipping = new Shipping();
        $shipping->setAddressRequired(true);
        $shipping->setType(3);
        $shipping->setCost(0);
        $shipping->setAddress($address);

        $sender = new Sender();
        $sender->setName('Fulano Silva');
        $sender->setEmail('fulano.silva@sandbox.pagseguro.com.br');
        $sender->setPhone('11', '30380000');
        $sender->setDocument(Document::cpf('72962940005'));
        $sender->setHash('{{hash_do_comprador}}');

        $boleto = Boleto::fromXml($xml);

        $instance = new Payment();
        $instance->setMode('default');
        $instance->setPayment($boleto);
        $instance->setSender($sender);
        $instance->setCurrency('BRL');
        $instance->setNotificationURL('https://sualoja.com.br/notificacao');
        $instance->addCartItem($cartItem);
        $instance->setExtraAmount(100.50);
        $instance->setReference('R123456');
        $instance->setShipping($shipping);

        $this->assertXmlStringEqualsXmlString($xml, $instance->toXml());
    }
}
