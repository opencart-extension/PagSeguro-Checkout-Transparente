<?php

namespace ValdeirPsr\PagSeguro\Exception;

use ValdeirPsr\PagSeguro\Domains\Environment;
use ValdeirPsr\PagSeguro\Domains\Logger\Logger;

class Auth extends \RuntimeException
{
    public function __construct(
        Environment $env,
        ?string $message = null,
        int $code = null,
        \Throwable $previous = null
    ) {
        Logger::emergency('Credenciais inválidas', [
            'e-mail' => $env->getEmail(),
            'token' => $env->getToken(),
        ]);
    }
}
