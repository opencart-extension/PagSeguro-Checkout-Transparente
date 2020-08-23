<?php

use PHPUnit\Framework\TestCase;
use ValdeirPsr\PagSeguro\Domains\Transaction;

class TransactionTest extends TestCase
{
    /**
     * @test
     */
    public function newInstance()
    {
        $instance = new Transaction();
        $this->assertInstanceOf(Transaction::class, $instance);
    }

    /**
     * @test
     */
    public function populateFromXml()
    {
        $xml = '
        <transaction>
            <date>2020-08-22T21:07:45.000-03:00</date>
            <code>084C7A72-E01B-4B4C-AD93-43218CEA0E8D</code>
            <reference>R123456</reference>
            <type>1</type>
            <status>1</status>
            <lastEventDate>2020-08-22T21:07:47.000-03:00</lastEventDate>
            <paymentMethod>
                <type>2</type>
                <code>202</code>
            </paymentMethod>
            <paymentLink>https://sandbox.pagseguro.uol.com.br/checkout/payment/booklet/print.jhtml?c=e1bb9a9775424b64c077b08a7694c40fc282fdb90e2e2f6c30cdd2152b1cd2873349e04c8a153ff0</paymentLink>
            <grossAmount>43.40</grossAmount>
            <discountAmount>0.00</discountAmount>
            <feeAmount>2.57</feeAmount>
            <netAmount>40.83</netAmount>
            <extraAmount>0.00</extraAmount>
            <installmentCount>1</installmentCount>
            <itemCount>2</itemCount>
            <items>
                <item>
                    <id>1</id>
                    <description>Antologia poética de Florbela Espanca</description>
                    <quantity>1</quantity>
                    <amount>27.80</amount>
                </item>
                <item>
                    <id>2</id>
                    <description>Poesia de Florbela Espanca</description>
                    <quantity>1</quantity>
                    <amount>15.60</amount>
                </item>
            </items>
            <sender>
                <name>Florbela Espanca</name>
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

        $instance = Transaction::fromXml($xml);

        $this->assertEquals('boleto', $instance->getPayment()->getMethod());
        $this->assertNotNull($instance->getStatus());
        $this->assertNotNull($instance->getPayment()->getPaymentLink());
        $this->assertEquals(43.40, $instance->getGrossAmount());
        $this->assertEquals(0.00, $instance->getDiscountAmount());
        $this->assertEquals(2.57, $instance->getFeeAmount());
        $this->assertEquals(40.83, $instance->getNetAmount());
        $this->assertEquals(0.00, $instance->getExtraAmount());
        $this->assertEquals(1, $instance->getInstallmentCount());
        $this->assertEquals(2, $instance->getItemCount());
    }
}
