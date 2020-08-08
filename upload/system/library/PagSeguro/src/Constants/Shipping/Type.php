<?php

namespace ValdeirPsr\PagSeguro\Constants\Shipping;

use ValdeirPsr\PagSeguro\Constants\AbstractEnum;

class Type extends AbstractEnum
{
    const PAC = 1;
    const SEDEX = 2;
    const UNKNOWN = 3;
}
