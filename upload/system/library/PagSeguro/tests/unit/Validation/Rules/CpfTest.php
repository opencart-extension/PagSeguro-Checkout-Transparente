<?php

use PHPUnit\Framework\TestCase;
use ValdeirPsr\PagSeguro\Validation\Validator;

class CpfTest extends TestCase
{
    /**
     * @dataProvider providerValid
     * @test
     */
    public function CheckValidArguments($input)
    {
        $v = Validator::cpf()->validate($input);
        $this->assertTrue($v);
    }

    /**
     * @dataProvider providerInvalid
     * @test
     */
    public function CheckInvalidArguments($input)
    {
        $v = Validator::cpf()->validate($input);
        $this->assertFalse($v);
    }

    public function providerValid()
    {
        return [
            ["641.155.660-19"],
            ["64115566019"],
            ["641.155.66019"], 
            ["641.15566019"], 
        ];
    }

    public function providerInvalid()
    {
        return [
            ["641.155.660-20"],
            ["64115566020"],
            ["641.155.66020"], 
            ["641.15566020"], 
        ];
    }
}
