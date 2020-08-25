<?php

namespace ValdeirPsr\PagSeguro\Validation\Rules;

/**
 * Valida um número de CPF
 *
 * @link https://github.com/Respect/Validation/blob/master/library/Rules/Cpf.php
 */
class Cpf implements IValidation
{
    /**
     * {@inheritDoc}
     */
    public function validate($input)
    {
        // Code ported from jsfromhell.com
        $c = preg_replace('/\D/', '', $input);

        if (strlen($c) != 11 || preg_match('/^' . $c[0] . '{11}$/', $c) || $c === '01234567890') {
            return false;
        }

        $n = 0;
        for ($s = 10, $i = 0; $s >= 2; ++$i, --$s) {
            $n += $c[$i] * $s;
        }

        if ($c[9] != (($n %= 11) < 2 ? 0 : 11 - $n)) {
            return false;
        }

        $n = 0;
        for ($s = 11, $i = 0; $s >= 2; ++$i, --$s) {
            $n += $c[$i] * $s;
        }

        $check = ($n %= 11) < 2 ? 0 : 11 - $n;

        return $c[10] == $check;
    }
}
