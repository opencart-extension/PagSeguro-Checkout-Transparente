<?php

namespace ValdeirPsr\PagSeguro\Validation\Rules;

/**
 * Valida um e-mail
 */
class Email implements IValidation
{
    /**
     * {@inheritDoc}
     */
    public function validate($input): bool
    {
        return filter_var($input, FILTER_VALIDATE_EMAIL);
    }
}
