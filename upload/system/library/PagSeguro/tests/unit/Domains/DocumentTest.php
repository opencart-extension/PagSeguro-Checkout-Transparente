<?php

use PHPUnit\Framework\TestCase;
use ValdeirPsr\PagSeguro\Domains\Document;

class DocumentTest extends TestCase
{
    /**
     * @test
     */
    public function newInstanceWithValidCpf()
    {
        $instance = Document::cpf('944.792.200-70');
        $this->assertEquals($instance->getValue(), '94479220070');
    }

    /**
     * @test
     */
    public function newInstanceWithInvalidCpf()
    {
        $this->expectException(InvalidArgumentException::class);
        $instance = Document::cpf('944.792.200-1');
    }

    /**
     * @test
     */
    public function newInstanceWithValidCnpj()
    {
        $instance = Document::cnpj('00.000.000/0001-91');
        $this->assertEquals($instance->getValue(), '00000000000191');
    }

    /**
     * @test
     */
    public function newInstanceWithInvalidCnpj()
    {
        $this->expectException(InvalidArgumentException::class);
        $instance = Document::cnpj('00.000.000/0001-90');
    }

    /**
     * @test
     */
    public function populateFromXml()
    {
        $xml = '
        <document>
            <type>CPF</type>
            <value>72962940005</value>
        </document>
        ';

        $instance = Document::fromXml($xml);

        $this->assertEqualsIgnoringCase('cpf', $instance->getType());
        $this->assertEquals('72962940005', $instance->getValue());
    }

    /**
     * @test
     */
    public function populateToXml()
    {
        $xml = '
        <document>
            <type>CPF</type>
            <value>72962940005</value>
        </document>
        ';

        $instance = Document::cpf('72962940005');

        $this->assertXmlStringEqualsXmlString($xml, $instance->toXml());
    }
}
