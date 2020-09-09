<?php

namespace ValdeirPsr\PagSeguro\Request;

use DOMDocument;
use ValdeirPsr\PagSeguro\Domains\Environment;
use ValdeirPsr\PagSeguro\Domains\Logger\Logger;
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

        Logger::info('Generates a new session for the user');

        $request = Factory::request($this->env);
        $request->post($url);
        $request->close();

        $sessionId = null;

        if ($request->isSuccess()) {
            $xml = $request->getResponse();
            $dom = new DOMDocument();
            $dom->loadXml($xml);

            $session = $dom->getElementsByTagName('id');

            if ($session->count() > 0) {
                $sessionId = trim($session->item(0)->textContent);
            }
        } elseif ($request->getHttpStatus() === 401) {
            throw new AuthException($this->env, 'Check your credentials', 1000);
        }

        Logger::info('Session generated', [
            'session_id' => $sessionId
        ]);

        return $sessionId;
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
