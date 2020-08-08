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
}
