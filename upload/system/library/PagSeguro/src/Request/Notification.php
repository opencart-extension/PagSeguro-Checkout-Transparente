<?php

namespace ValdeirPsr\PagSeguro\Request;

use ValdeirPsr\PagSeguro\Domains\Environment;
use ValdeirPsr\PagSeguro\Domains\Transaction;
use ValdeirPsr\PagSeguro\Domains\Logger\Logger;
use ValdeirPsr\PagSeguro\Exception\Auth as AuthException;
use ValdeirPsr\PagSeguro\Exception\PagSeguroRequest as PagSeguroRequestException;

class Notification
{
    private $env;

    public function __construct(Environment $env)
    {
        $this->env = $env;
    }

    public function capture(string $notificationId)
    {
        return Transaction::fromXml($this->request($notificationId));
    }

    /**
     * {@inheritDoc}
     */
    protected function buildUrl(string $path = ''): string
    {
        return Factory::url($this->env, "v3/transactions/notifications/{$path}", [
            'email' => $this->env->getEmail(),
            'token' => $this->env->getToken()
        ]);
    }

    /**
     * {@inheritDoc}
     */
    protected function request(string $path = '')
    {
        $url = $this->buildUrl($path);

        Logger::info('Checando uma notificação', [
            'e-mail' => $this->env->getEmail(),
            'token' => $this->env->getToken(),
            'code' => $path
        ]);

        $request = Factory::request($this->env);
        $request->get($url);
        $request->close();

        if ($request->isSuccess()) {
            $response = $request->getResponse();

            Logger::info('Notificação verificada', [
                'e-mail' => $this->env->getEmail(),
                'token' => $this->env->getToken(),
                'code' => $path,
                'response' => $response
            ]);

            return $response;
        } elseif ($request->getHttpStatus() === 401) {
            throw new AuthException($this->env, 'Check your credentials', 1000);
        } else {
            throw new PagSeguroRequestException($this->env, $request, $path);
        }
    }
}
