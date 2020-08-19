<?php

namespace ValdeirPsr\PagSeguro\Domains\PaymentMethod;

class Boleto extends AbstractPaymentMethod
{
    /** @var string Link do boleto (somente leitura) */
    private $paymentLink;

    public function __construct()
    {
        parent::__construct('boleto');
    }

    /**
     * @return string
     */
    public function getPaymentLink(): string
    {
        return $this->paymentLink;
    }
}
