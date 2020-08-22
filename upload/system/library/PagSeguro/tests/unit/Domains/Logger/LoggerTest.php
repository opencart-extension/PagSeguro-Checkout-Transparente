<?php

use PHPUnit\Framework\TestCase;
use ValdeirPsr\PagSeguro\Domains\Logger\Logger;
use Monolog\Logger as Monolog;

class LoggerTest extends TestCase
{
    /**
     * @test
     */
    public function newInstance()
    {
        $this->assertInstanceOf(Monolog::class, Logger::getInstance());
    }
}
