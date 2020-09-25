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

        Logger::debug('Gerando sessão para o usuário', [
            'e-mail' => $this->env->getEmail(),
            'token' => $this->env->getToken()
        ]);

        $request = Factory::request($this->env);
        $request->post($url);
        $request->close();

        if ($request->isSuccess()) {
            $xml = $request->getResponse();
            $dom = new DOMDocument();
            $dom->loadXml($xml);

            $session = $dom->getElementsByTagName('id');

            if ($session->count() > 0) {
                $session_id = trim($session->item(0)->textContent);

                Logger::debug('Token gerado', [
                    'e-mail' => $this->env->getEmail(),
                    'token' => $this->env->getToken(),
                    'session' => $session_id
                ]);

                return $session_id;
            }
        } elseif ($request->getHttpStatus() === 401) {
            throw new AuthException($this->env, 'Check your credentials', 1000);
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
