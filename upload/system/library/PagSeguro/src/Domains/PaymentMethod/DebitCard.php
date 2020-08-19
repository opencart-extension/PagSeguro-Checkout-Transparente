<?php

namespace ValdeirPsr\PagSeguro\Domains\PaymentMethod;

class DebitCard extends AbstractPaymentMethod
{
    /** @var string Link de pagamento (somente leitura) */
    private $paymentLink;

    public function __construct()
    {
        parent::__construct('eft');
    }

    /**
     * @return string
     */
    public function getPaymentLink(): string
    {
        return $this->paymentLink;
    }
}
