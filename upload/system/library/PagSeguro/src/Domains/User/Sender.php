<?php

namespace ValdeirPsr\PagSeguro\Domains\User;

class Sender extends AbstractUser
{
    /** @var string (fingerprint) gerado pelo JavaScript do PagSeguro */
    private $hash;

    /**
     * Define o hash (fingerprint) gerado pelo JavaScript do PagSeguro
     * 
     * @param string $value
     * 
     * @return self
     */
    public function setHash(string $value): self
    {
        $this->hash = $value;
        return $this;
    }

    /**
     * @return string Retorna o hash (fingerprint)
     */
    public function getHash(): string
    {
        return $this->hash;
    }
}
