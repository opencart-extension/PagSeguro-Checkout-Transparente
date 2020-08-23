<?php

use PHPUnit\Framework\TestCase;
use ValdeirPsr\PagSeguro\Domains\PaymentMethod\AbstractPaymentMethod;
use ValdeirPsr\PagSeguro\Domains\PaymentMethod\CreditCard;
use ValdeirPsr\PagSeguro\Domains\User\Holder;
use ValdeirPsr\PagSeguro\Domains\Address;
use ValdeirPsr\PagSeguro\Domains\Document;

class CreditCardTest extends TestCase
{
    /**
     * @test
     */
    public function newInstance()
    {
        $instance = new CreditCard();
        $this->assertInstanceOf(CreditCard::class, $instance);
        $this->assertInstanceOf(AbstractPaymentMethod::class, $instance);
    }

    /**
     * @test
     */
    public function getMethodShouldReturnCreditCard()
    {
        $instance = new CreditCard();
        $this->assertEquals('creditcard', $instance->getMethod());
    }

    /**
     * @test
     */
    public function populateFromXml()
    {
        $xml = '
        <transaction>
            <date>2019-01-29T14:37:00.000-02:00</date>
            <code>D58A27DC-E03A-47E5-A20A-63AE1B80C5B6</code>
            <reference>REF1234</reference>
            <type>1</type>
            <status>1</status>
            <lastEventDate>2019-01-29T14:37:00.000-02:00</lastEventDate>
            <paymentMethod>
                <type>1</type>
                <code>101</code>
            </paymentMethod>
            <gatewaySystem>
                <type>cielo</type>
                <rawCode xsi:nil="true" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"/>
                <rawMessage xsi:nil="true" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"/>
                <normalizedCode xsi:nil="true" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"/>
                <normalizedMessage xsi:nil="true" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"/>
                <authorizationCode>0</authorizationCode>
                <nsu>0</nsu>
                <tid>0</tid>
                <establishmentCode>1056784170</establishmentCode>
                <acquirerName>CIELO</acquirerName>
            </gatewaySystem>
        </transaction>
        ';

        $instance = CreditCard::fromXml($xml);

        $this->assertEquals(1, $instance->getType());
        $this->assertEquals(101, $instance->getCode());
        $this->assertEqualsIgnoringCase('CreditCard', $instance->getMethod());
    }

    /**
     * @test
     */
    public function populateToXml()
    {
        $xml = '
        <creditCard>
            <token>90c7dd13db854786b05f3896c6dd56d7</token>
            <installment>
                <quantity>3</quantity>
                <value>14.14</value>
                <noInterestInstallmentQuantity>3</noInterestInstallmentQuantity>
            </installment>
            <holder>
                <name>Nome impresso no cartao</name>
                <birthDate>20/10/1980</birthDate>
                <documents>
                    <document>
                        <type>CPF</type>
                        <value>22111944785</value>
                    </document>
                </documents>
                <phone>
                    <areaCode>11</areaCode>
                    <number>999991111</number>
                </phone>
            </holder>
            <billingAddress>
                <street>Av. Brigadeiro Faria Lima</street>
                <number>1384</number>
                <complement>1 andar</complement>
                <district>Jardim Paulistano</district>
                <city>Sao Paulo</city>
                <state>SP</state>
                <country>BRA</country>
                <postalCode>01452002</postalCode>
            </billingAddress>
        </creditCard>
        ';

        $document = Document::cpf('22111944785');

        $holder = new Holder();
        $holder->setName('Nome impresso no cartao');
        $holder->setDocument($document);
        $holder->setBirthdate(\DateTime::createFromFormat('d/m/Y', '20/10/1980'));
        $holder->setPhone('11', '999991111');
        
        $billingAddress = new Address();
        $billingAddress->setStreet('Av. Brigadeiro Faria Lima');
        $billingAddress->setNumber('1384');
        $billingAddress->setComplement('1 andar');
        $billingAddress->setDistrict('Jardim Paulistano');
        $billingAddress->setCity('Sao Paulo');
        $billingAddress->setState('SP');
        $billingAddress->setPostalCode('01452002');

        $instance = new CreditCard();
        $instance->setToken('90c7dd13db854786b05f3896c6dd56d7');
        $instance->setInstallmentQuantity(3);
        $instance->setInstallmentValue(14.14);
        $instance->setHolder($holder);
        $instance->setBillingAddress($billingAddress);
        $instance->setNoInterestInstallmentQuantity(3);

        $this->assertXmlStringEqualsXmlString($xml, $instance->toXml());
    }
}
