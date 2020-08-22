<?php

use PHPUnit\Framework\TestCase;
use ValdeirPsr\PagSeguro\Domains\User\Holder;
use ValdeirPsr\PagSeguro\Domains\Document;

class HolderTest extends TestCase
{
    /**
     * @test
     */
    public function newInstance()
    {
        $instance = new Holder;
        $this->assertInstanceOf(Holder::class, $instance);
    }

    /**
     * @test
     */
    public function populateFromXml()
    {
        $xml = '
        <holder>
            <name>Nome impresso no cartao</name>
            <documents>
                <document>
                    <type>CPF</type>
                    <value>22111944785</value>
                </document>
            </documents>
            <birthDate>20/10/1980</birthDate>
            <phone>
                <areaCode>11</areaCode>
                <number>999991111</number>
            </phone>
        </holder>
        ';

        $instance = Holder::fromXml($xml);

        $this->assertEquals('Nome impresso no cartao', $instance->getName());
        $this->assertEqualsIgnoringCase('CPF', $instance->getDocument()->getType());
        $this->assertEquals('22111944785', $instance->getDocument()->getValue());
        $this->assertEquals('20/10/1980', $instance->getBirthDate()->format('d/m/Y'));
        $this->assertEquals([
            'areaCode' => '11',
            'number' => '999991111'
        ], $instance->getPhone());
    }

    /**
     * @test
     */
    public function populateToXml()
    {
        $xml = '
        <holder>
            <name>Nome impresso no cartao</name>
            <documents>
                <document>
                    <type>CPF</type>
                    <value>22111944785</value>
                </document>
            </documents>
            <birthDate>20/10/1980</birthDate>
            <phone>
                <areaCode>11</areaCode>
                <number>999991111</number>
            </phone>
        </holder>
        ';

        $instance = new Holder();
        $instance->setName('Nome impresso no cartao');
        $instance->setPhone('11', '999991111');
        $instance->setBirthDate(\DateTime::createFromFormat('Y-m-d', '1980-10-20'));
        $instance->setDocument(Document::cpf('22111944785'));

        $this->assertXmlStringEqualsXmlString($xml, $instance->toXml());
    }
}
