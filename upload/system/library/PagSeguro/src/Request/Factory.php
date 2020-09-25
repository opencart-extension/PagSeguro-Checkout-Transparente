<?php

namespace ValdeirPsr\PagSeguro\Request;

use Curl\Curl;
use ValdeirPsr\PagSeguro\Domains\Environment;

class Factory
{
    private const URL_SANDBOX = 'https://ws.sandbox.pagseguro.uol.com.br/';
    private const URL_PRODUCTION = 'https://ws.pagseguro.uol.com.br/';

    public const USER_AGENT = 'PagSeguro SDK for OpenCart v1.0.0';

    /**
     * Cria classe para requisição
     *
     * @param Environment $env
     *
     * @return Curl
     */
    public static function request(Environment $env): Curl
    {
        $instance = new Curl();
        $instance->setUserAgent(self::USER_AGENT);
        $instance->setOpt(CURLOPT_SSL_VERIFYPEER, !$env->isSandbox());

        return $instance;
    }

    /**
     * Cria uma URL
     */
    public static function url(Environment $env, string $path, array $parameters = []): string
    {
        return sprintf(
            '%s%s?%s',
            $env->isSandbox() ? self::URL_SANDBOX : self::URL_PRODUCTION,
            ltrim($path, '/'),
            http_build_query($parameters)
        );
    }
}
