<?php

use PHPUnit\Framework\TestCase;
use ValdeirPsr\PagSeguro\Domains\Sender;

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
}
