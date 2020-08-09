<?php

namespace ValdeirPsr\PagSeguro\Domains;

use ValdeirPsr\PagSeguro\Validation\Validator as v;

class Sender
{
    /** @var string (fingerprint) gerado pelo JavaScript do PagSeguro */
    private $hash;

    /** @var string */
    private $name;

    /** @var string */
    private $email;

    /** @var string[] */
    private $phone = [];

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

    /**
     * Define o nome do comprador
     * 
     * @param string $value
     * 
     * @return self
     */
    public function setName(string $value): self
    {
        $this->name = $value;
        return $this;
    }

    /**
     * @return string Retorna o nome do comprador
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Define o e-mail do comprador
     * 
     * @param string $value
     * 
     * @return self
     */
    public function setEmail(string $value): self
    {
        if (!v::email()->validate($value)) {
            throw new \InvalidArgumentException("E-mail $value is invalid.");
        }

        $this->email = $value;
        return $this;
    }

    /**
     * @return string Retorna o e-mail do comprador
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Define o número de telefone do comprador
     * O código removerá todos os caracteres não númericos.
     * 
     * @param string|int $areaCode
     * @param string|int $number
     * 
     * @return self
     */
    public function setPhone($areaCode, $number): self
    {
        $this->phone = [
            "areaCode" => preg_replace('/\D/', '', $areaCode),
            "number" => preg_replace('/\D/', '', $number)
        ];
        return $this;
    }

    /**
     * @return array Retorna o contato do comprador
     */
    public function getPhone(): array
    {
        return $this->phone;
    }
}
