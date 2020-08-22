<?php

use PHPUnit\Framework\TestCase;
use ValdeirPsr\PagSeguro\Domains\Environment;
use ValdeirPsr\PagSeguro\Request\Factory;
use Curl\Curl;

class FactoryTest extends TestCase
{
    /**
     * @test
     */
    public function newInstance()
    {
        $env = Environment::sandbox('pagseguro@valdeir.dev', '1234567890');
        $instance = Factory::request($env);

        $this->assertInstanceOf(Curl::class, $instance);
        $this->assertEquals('PagSeguro SDK for OpenCart v1.0.0', Factory::USER_AGENT);
    }

    /**
     * @test
     */
    public function checkUrlBuilder()
    {
        $expected = 'https://ws.sandbox.pagseguro.uol.com.br/checkout/v2/installments.json?sessionId=0123456789&amount=1000.00&creditCardBrand=master';

        $env = Environment::sandbox('pagseguro@valdeir.dev', '1234567890');
        $url = Factory::url($env, 'checkout/v2/installments.json', [
            'sessionId' => '0123456789',
            'amount' => '1000.00',
            'creditCardBrand' => 'master'
        ]);

        $this->assertEquals($expected, $url);
    }
}
