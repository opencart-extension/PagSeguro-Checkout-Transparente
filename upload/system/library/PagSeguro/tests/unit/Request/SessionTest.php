<?php

use PHPUnit\Framework\TestCase;
use ValdeirPsr\PagSeguro\Domains\Environment;
use ValdeirPsr\PagSeguro\Domains\Logger\Logger;
use ValdeirPsr\PagSeguro\Request\Factory;
use ValdeirPsr\PagSeguro\Request\Session;
use ValdeirPsr\PagSeguro\Exception\Auth as AuthException;
use ValdeirPsr\PagSeguro\Exception\PagSeguroRequest as PagSeguroRequestException;

class SessionTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        Logger::getInstance([
            'enabled' => false
        ]);
    }

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
            ->willReturn(getenv('SERVER_URL') . 'v2/sessions/valid');

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
            ->willReturn(getenv('SERVER_URL') . 'v2/sessions/invalid');

        $stub->generate();
    }

    /**
     * @test
     */
    public function whenEnteringServerIsDownShouldReturnAnError()
    {
        $this->expectException(PagSeguroRequestException::class);

        $env = Environment::sandbox('pagseguro@valdeir.dev', '');

        $stub = $this->getMockBuilder(Session::class)
            ->setConstructorArgs([$env])
            ->setMethods(['buildUrl'])
            ->getMock();

        $stub->expects($this->any())
            ->method('buildUrl')
            ->willReturn(getenv('SERVER_URL') . 'v2/sessions/invalid-2');

        $stub->generate();
    }
}
