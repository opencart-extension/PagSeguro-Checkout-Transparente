<?php

namespace ValdeirPsr\PagSeguro\Domains;

use ValdeirPsr\PagSeguro\Constants\Shipping\Type;
use ValdeirPsr\PagSeguro\Validation\Validator as v;

class Shipping
{
    /** @var int Tipo de Frete (1 - PAC; 2 - SEDEX; 3 - Outros) */
    private $type;

    /** @var float Valor do frete */
    private $cost;

    /** @var bool */
    private $addressRequired;

    /** @var Address */
    private $address;

    /**
     * Define o tipo de frete.
     * 
     * @see \ValdeirPsr\PagSeguro\Constants\Shipping\Type
     * 
     * @param int $type
     * 
     * @throws \InvalidArgumentException Caso o tipo seja inválido
     * 
     * @return self
     */
    public function setType(int $type): self
    {
        if (!Type::has($type) && !Type::isValidValue($type)) {
            throw new \InvalidArgumentException('Shipping type is invalid', 3000);
        }

        $this->type = $type;
        return $this;
    }

    /**
     * @return int Retorna o tipo de frete
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * Define o valor do frete
     * 
     * @param float $value
     * 
     * @throws \InvalidArgumentException Caso o valor possua mais de duas casas decimais
     * 
     * @return self
     */
    public function setCost(float $value): self
    {
        if (!v::Money(2)->validate($value)) {
            throw new \InvalidArgumentException('Cost invalid. The value must have two decimal places. Was: ' . $value);
        }

        $this->cost = $value;
        return $this;
    }

    /**
     * @return float Retorna o valor do frete
     */
    public function getCost(): float
    {
        return $this->cost;
    }

    /**
     * Define se o endereço de envio é obrigatório
     * 
     * @param bool $value
     * 
     * @return self
     */
    public function setAddressRequired(bool $value): self
    {
        $this->addressRequired = $value;
        return $this;
    }

    /**
     * @return bool Retorna true se o endereço de envio é obrigatório;
     *              caso contrário, retorna false.
     */
    public function getAddressRequired(): bool
    {
        return $this->addressRequired;
    }

    /**
     * Define o endereço de envio
     * 
     * @param Address $value
     * 
     * @return self
     */
    public function setAddress(Address $value): self
    {
        $this->address = $value;
        return $this;
    }

    /**
     * @return Address Retorna o endereço de entrega
     */
    public function getAddress(): Address
    {
        return $this->address;
    }
}
