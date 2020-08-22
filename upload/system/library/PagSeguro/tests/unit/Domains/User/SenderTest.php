<?php

use PHPUnit\Framework\TestCase;
use ValdeirPsr\PagSeguro\Domains\User\Sender;
use ValdeirPsr\PagSeguro\Domains\Document;

class SenderTest extends TestCase
{
    /**
     * @test
     */
    public function newInstance()
    {
        $instance = new Sender;
        $this->assertInstanceOf(Sender::class, $instance);
    }

    /**
     * @test
     */
    public function defineSenderEmailInvalidShouldGiveError()
    {
        $this->expectException(InvalidArgumentException::class);
        $instance = new Sender;
        $instance->setEmail('@valdeir.dev');
    }

    /**
     * @test
     */
    public function defineSenderPhoneValid()
    {
        $instance = new Sender;
        $instance->setPhone('71', '9 1234-5678');

        $this->assertEquals([
            "areaCode" => '71',
            "number" => '912345678'
        ], $instance->getPhone());
    }

    /**
     * @test
     */
    public function populateFromXml()
    {
        $xml = '
        <sender>
            <name>Fulano Silva</name>
            <email>fulano.silva@sandbox.pagseguro.com.br</email>
            <phone>
                <areaCode>11</areaCode>
                <number>30380000</number>
            </phone>
            <documents>
                <document>
                    <type>cpf</type>
                    <value>72962940005</value>
                </document>
            </documents>
            <hash>{{hash_do_comprador}}</hash>
        </sender>
        ';

        $instance = Sender::fromXml($xml);

        $this->assertEquals('Fulano Silva', $instance->getName());
        $this->assertEquals('fulano.silva@sandbox.pagseguro.com.br', $instance->getEmail());
        $this->assertEquals('{{hash_do_comprador}}', $instance->getHash());
        $this->assertEquals([
            'areaCode' => '11',
            'number' => '30380000'
        ], $instance->getPhone());
        $this->assertEquals(
            [
                'cpf',
                '72962940005'
            ], [
                $instance->getDocument()->getType(),
                $instance->getDocument()->getValue(),
            ]
        );
    }

    /**
     * @test
     */
    public function populateToXml()
    {
        $xml = '
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
                    <type>cpf</type>
                    <value>72962940005</value>
                </document>
            </documents>
        </sender>
        ';

        $instance = new Sender();
        $instance->setName('Fulano Silva');
        $instance->setEmail('fulano.silva@sandbox.pagseguro.com.br');
        $instance->setPhone('11', '30380000');
        $instance->setDocument(Document::cpf('72962940005'));
        $instance->setHash('{{hash_do_comprador}}');

        $this->assertXmlStringEqualsXmlString($xml, $instance->toXml());
    }
}
