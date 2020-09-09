<?php

namespace ValdeirPsr\PagSeguro\Exception;

use ValdeirPsr\PagSeguro\Domains\Environment;
use ValdeirPsr\PagSeguro\Domains\Logger\Logger;

class Auth extends \RuntimeException
{
    public function __construct(Environment $env = null, string $message = null, int $code = null, Throwable $previous = null)
    {
        $context = [];

        if ($env) {
            $context = [
                'E-mail' => $env->getEmail(),
                'Token' => $env->getToken(),
                'Is Sandbox' => $env->isSandbox() ? 'Yes' : 'No'
            ];
        }

        Logger::emergency('Check your credentials', $context);
    }
}
