<?php

namespace ValdeirPsr\PagSeguro\Validation;

class Validator
{
    public static function __callStatic($name, $args)
    {
        return Factory::getDefaultInstance()->rule($name, $args);
    }
}
