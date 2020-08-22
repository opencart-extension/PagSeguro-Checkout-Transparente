<?php

use PHPUnit\Framework\TestCase;
use ValdeirPsr\PagSeguro\Domains\Environment;
use ValdeirPsr\PagSeguro\Request\Factory;
use ValdeirPsr\PagSeguro\Request\Session;
use ValdeirPsr\PagSeguro\Exception\Auth as AuthException;

class SessionTest extends TestCase
{
    /**
     * @test
     */
    public function newInstance()
    {
        $env = Environment::sandbox('pagseguro@valdeir.dev', '1234567890');
        $instance = new Session($env);

        $this->assertInstanceOf(Session::class, $instance);
    }

    /**
     * @test
     */
    public function generatingANewSessionWithValidDataShouldNotReturnAnError()
    {
        $env = Environment::sandbox('pagseguro@valdeir.dev', '');

        $stub = $this->getMockBuilder(Session::class)
            ->setConstructorArgs([$env])
            ->setMethods(['buildUrl'])
            ->getMock();

        $stub->expects($this->any())
            ->method('buildUrl')
            ->willReturn('https://f3528d51-6219-4b80-8bd3-3ab112b8094f.mock.pstmn.io/v2/sessions/valid');

        $result = $stub->generate();
        $this->assertNotNull($result);
    }

    /**
     * @test
     */
    public function whenEnteringInvalidCredentialsShouldReturnAnError()
    {
        $this->expectException(AuthException::class);
        
        $env = Environment::sandbox('pagseguro@valdeir.dev', '');

        $stub = $this->getMockBuilder(Session::class)
            ->setConstructorArgs([$env])
            ->setMethods(['buildUrl'])
            ->getMock();

        $stub->expects($this->any())
            ->method('buildUrl')
            ->willReturn('https://f3528d51-6219-4b80-8bd3-3ab112b8094f.mock.pstmn.io/v2/sessions/invalid');

        $stub->generate();
    }
}
