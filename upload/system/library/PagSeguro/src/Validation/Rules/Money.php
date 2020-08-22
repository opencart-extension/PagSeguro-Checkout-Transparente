<?php

namespace ValdeirPsr\PagSeguro\Validation\Rules;

/**
 * Valida o número de casas decimais
 */
class Money implements IValidation
{
    private $decimals;

    public function __construct(int $decimals = 2)
    {
        $this->decimals = $decimals + 1;
    }

    /**
     * {@inheritDoc}
     */
    public function validate($input)
    {
        return strlen(strrchr($input, '.')) <= $this->decimals;
    }
}
