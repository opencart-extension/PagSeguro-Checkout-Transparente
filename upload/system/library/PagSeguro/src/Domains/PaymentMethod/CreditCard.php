<?php

namespace ValdeirPsr\PagSeguro\Domains\PaymentMethod;

use ValdeirPsr\PagSeguro\Domains\User\Holder;
use ValdeirPsr\PagSeguro\Domains\Address;

class CreditCard extends AbstractPaymentMethod
{
    /** @var string */
    private $token;

    /** @var int */
    private $installmentQuantity;

    /** @var float */
    private $installmentValue;

    /** @var string */
    private $holder;

    /** @var Address */
    private $billingAddress;

    public function __construct()
    {
        parent::__construct('creditcard');
    }

    /**
     * Define o token, gerado pelo JavaScript, do cartão
     * 
     * @param string $value
     * 
     * @return self
     */
    public function setToken(string $value): self
    {
        $this->token = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * Define o número de parcelas
     * 
     * @param int $value
     * 
     * @return self
     */
    public function setInstallmentQuantity(int $value): self
    {
        $this->installmentQuantity = $value;

        return $this;
    }

    /**
     * @return int
     */
    public function getInstallmentQuantity(): int
    {
        return $this->installmentQuantity;
    }

    /**
     * Define o valor da parcela
     * 
     * @param float $value
     * 
     * @return self
     */
    public function setInstallmentValue(float $value): self
    {
        $this->installmentValue = $value;

        return $this;
    }

    /**
     * @return float
     */
    public function getInstallmentValue(): float
    {
        return $this->installmentValue;
    }

    /**
     * Define os dados do títular do cartão
     * 
     * @param Holder $value
     * 
     * @return self
     */
    public function setHolder(Holder $value): self
    {
        $this->holder = $value;

        return $this;
    }

    /**
     * @return Holder
     */
    public function getHolder(): Holder
    {
        return $this->holder;
    }

    /**
     * Define os dados de endereço para o pagamento
     * 
     * @param Address $value
     * 
     * @return self
     */
    public function setBillingAddress(Address $value): self
    {
        $this->billingAddress = $value;

        return $this;
    }

    /**
     * @return Address
     */
    public function getBillingAddress(): Address
    {
        return $this->billingAddress;
    }
}
