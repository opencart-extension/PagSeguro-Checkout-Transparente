<?php

use PHPUnit\Framework\TestCase;
use ValdeirPsr\PagSeguro\Validation\Validator;

class CnpjTest extends TestCase
{
    /**
     * @dataProvider providerValid
     * @test
     */
    public function CheckValidArguments($input)
    {
        $v = Validator::Cnpj()->validate($input);
        $this->assertTrue($v);
    }

    /**
     * @dataProvider providerInvalid
     * @test
     */
    public function CheckInvalidArguments($input)
    {
        $v = Validator::Cnpj()->validate($input);
        $this->assertFalse($v);
    }

    public function providerValid()
    {
        return [
            ["00.000.000/0001-91"],
            ["23.572.852/0001-59"],
            ["27.697.5010001-25"], 
            ["27.697.501/000125"], 
        ];
    }

    public function providerInvalid()
    {
        return [
            ["37.887.406/0001-49"],
            ["38.887.406/0001-48"]
        ];
    }
}
