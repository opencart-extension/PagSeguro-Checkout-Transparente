<?php

use PHPUnit\Framework\TestCase;
use ValdeirPsr\PagSeguro\Domains\User\Holder;

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
}
