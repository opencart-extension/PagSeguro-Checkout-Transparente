<?php

namespace ValdeirPsr\PagSeguro\Validation\Rules;

interface IValidation
{
    /**
     * Valida uma informação
     *
     * @return bool
     */
    public function validate($value);
}
