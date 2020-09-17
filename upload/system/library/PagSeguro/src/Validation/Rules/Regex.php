<?php

namespace ValdeirPsr\PagSeguro\Validation\Rules;

/**
 * Valida um valor através de uma expressão regular
 */
class Regex implements IValidation
{
    private $regex;

    public function __construct(string $regex)
    {
        $this->regex = $regex;
    }

    /**
     * {@inheritDoc}
     */
    public function validate($input): bool
    {
        return preg_match($this->regex, $input);
    }
}
