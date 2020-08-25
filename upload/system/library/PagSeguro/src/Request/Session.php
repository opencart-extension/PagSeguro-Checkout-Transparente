<?php

namespace ValdeirPsr\PagSeguro\Request;

use DOMDocument;
use ValdeirPsr\PagSeguro\Domains\Environment;
use ValdeirPsr\PagSeguro\Exception\Auth as AuthException;

class Session
{
    private $env;

    public function __construct(Environment $env)
    {
        $this->env = $env;
    }

    /**
     * Gera uma sessão
     *
     * @throws AuthException Caso as credenciais sejam inválidas
     *
     * @return string
     */
    public function generate(): string
    {
        $url = $this->buildUrl();

        $request = Factory::request($this->env);
        $request->post($url);
        $request->close();

        if ($request->isSuccess()) {
            $xml = $request->getResponse();
            $dom = new DOMDocument();
            $dom->loadXml($xml);

            $session = $dom->getElementsByTagName('id');

            if ($session->count() > 0) {
                return trim($session->item(0)->textContent);
            }
        } elseif ($request->getHttpStatus() === 401) {
            throw new AuthException('Check your credentials', 1000);
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function buildUrl(): string
    {
        return Factory::url($this->env, 'v2/sessions', [
            'email' => $this->env->getEmail(),
            'token' => $this->env->getToken()
        ]);
    }
}
