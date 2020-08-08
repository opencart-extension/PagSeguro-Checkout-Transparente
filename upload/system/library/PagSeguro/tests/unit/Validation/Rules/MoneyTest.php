<?php

use PHPUnit\Framework\TestCase;
use ValdeirPsr\PagSeguro\Validation\Validator;

class MoneyTest extends TestCase
{
    /**
     * @dataProvider providerValid
     * @test
     */
    public function CheckValidArguments($decimals, $input)
    {
        $v = Validator::Money($decimals)->validate($input);
        $this->assertTrue($v);
    }

    /**
     * @dataProvider providerInvalid
     * @test
     */
    public function CheckInvalidArguments($decimals, $input)
    {
        $v = Validator::Money($decimals)->validate($input);
        $this->assertFalse($v);
    }

    public function providerValid()
    {
        return [
            [1, 1307199.3],
            [2, 130719.93],
            [4, 1307.1993],
        ];
    }

    public function providerInvalid()
    {
        return [
            [2, 1307199.3],
            [3, 130719.93],
            [5, 1307.1993],
        ];
    }
}
