<?php

use PHPUnit\Framework\TestCase;
use ValdeirPsr\PagSeguro\Domains\PaymentMethod\AbstractPaymentMethod;
use ValdeirPsr\PagSeguro\Domains\PaymentMethod\DebitCard;

class DebitCardTest extends TestCase
{
    /**
     * @test
     */
    public function newInstance()
    {
        $instance = new DebitCard();
        $this->assertInstanceOf(DebitCard::class, $instance);
        $this->assertInstanceOf(AbstractPaymentMethod::class, $instance);
    }

    /**
     * @test
     */
    public function getMethodShouldReturnEFT()
    {
        $instance = new DebitCard();
        $this->assertEquals('eft', $instance->getMethod());
    }

    /**
     * @test
     */
    public function populateFromXml()
    {
        $xml = '
        <transaction>
            <date>2019-01-29T15:01:52.000-02:00</date>
            <code>96B58137-065E-4669-B055-D12B2FB38E34</code>
            <reference>R123456</reference>
            <type>1</type>
            <status>1</status>
            <lastEventDate>2019-01-29T15:01:55.000-02:00</lastEventDate>
            <paymentMethod>
                <type>2</type>
                <code>202</code>
            </paymentMethod>
            <paymentLink>
                https://sandbox.pagseguro.uol.com.br/checkout/payment/booklet/print.jhtml?c=b7989f954e7253974d2bf0bbe6c80cfb6caa0e146b13d70d90ffbb3243b22302c4600a923e6f02b0
            </paymentLink>
            <grossAmount>2.00</grossAmount>
            <discountAmount>0.00</discountAmount>
            <feeAmount>0.48</feeAmount>
            <netAmount>1.52</netAmount>
            <extraAmount>0.00</extraAmount>
            <installmentCount>1</installmentCount>
            <itemCount>1</itemCount>
            <items>
                <item>
                    <id>1</id>
                    <description>Descricao do item a ser vendido</description>
                    <quantity>2</quantity>
                    <amount>1.00</amount>
                </item>
            </items>
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
            </sender>
            <shipping>
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
        </transaction>
        ';

        $instance = DebitCard::fromXml($xml);

        $this->assertEquals(2, $instance->getType());
        $this->assertEquals(202, $instance->getCode());
        $this->assertEquals('eft', $instance->getMethod());
        $this->assertEquals('https://sandbox.pagseguro.uol.com.br/checkout/payment/booklet/print.jhtml?c=b7989f954e7253974d2bf0bbe6c80cfb6caa0e146b13d70d90ffbb3243b22302c4600a923e6f02b0', $instance->getPaymentLink());
    }
}
