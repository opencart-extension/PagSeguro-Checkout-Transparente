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
        $this->decimals = $decimals;        
    }

    /**
     * {@inheritDoc}
     */
    public function validate($input)
    {
        return strlen(strrchr($input, '.')) == ($this->decimals + 1);
    }
}
