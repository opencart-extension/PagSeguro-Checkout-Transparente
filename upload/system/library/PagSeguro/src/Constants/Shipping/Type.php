<?php

namespace ValdeirPsr\PagSeguro\Constants\Shipping;

use ValdeirPsr\PagSeguro\Constants\AbstractEnum;

class Type extends AbstractEnum
{
    public const PAC = 1;
    public const SEDEX = 2;
    public const UNKNOWN = 3;
}
