<?php

namespace ValdeirPsr\PagSeguro\Domains;

/**
 * Define o ambiente de desenvolvimento
 */
class Environment
{
    private $isSandbox;
    private $email;
    private $token;

    /**
     * @param bool $isSandbox
     * @param string $email
     * @param string $token
     */
    private function __construct(bool $isSandbox, string $email, string $token)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("$email is invalid", 1000);
        }

        $this->isSandbox = $isSandbox;
        $this->email = $email;
        $this->token = $token;
    }

    /**
     * Define o ambiente como teste
     *
     * @param string $email
     * @param string $token
     *
     * @return self
     */
    public static function sandbox(string $email, string $token)
    {
        return new self(true, $email, $token);
    }

    /**
     * Define o ambiente como produção
     *
     * @param string $email
     * @param string $token
     *
     * @return self
     */
    public static function production(string $email, string $token)
    {
        return new self(false, $email, $token);
    }

    /**
     * Verifica se o ambiente está no modo de teste
     *
     * @return bool
     */
    public function isSandbox()
    {
        return $this->isSandbox;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }
}
