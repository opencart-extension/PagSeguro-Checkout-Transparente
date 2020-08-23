<?php

namespace ValdeirPsr\PagSeguro\Request;

use ValdeirPsr\PagSeguro\Domains\Environment;
use ValdeirPsr\PagSeguro\Domains\Payment;
use ValdeirPsr\PagSeguro\Domains\Transaction;
use ValdeirPsr\PagSeguro\Exception\Auth as AuthException;

class Sale
{
    private $env;

    public function __construct(Environment $env)
    {
        $this->env = $env;
    }

    public function create(Payment $payment)
    {
        $url = $this->buildUrl();

        $request = Factory::request($this->env);
        $request->setHeader('Content-Type', 'application/xml; charset=ISO-8859-1');
        $request->post($url, $payment->toXml());
        $request->close();

        if ($request->isSuccess()) {
            return Transaction::fromXml($request->getResponse());
        } elseif ($request->getHttpStatus() === 401) {
            throw new AuthException('Check your credentials', 1000);
        } else {
            throw new PagSeguroRequestException($request);
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function buildUrl(): string
    {
        return Factory::url($this->env, 'v2/transactions', [
            'email' => $this->env->getEmail(),
            'token' => $this->env->getToken()
        ]);
    }
}
