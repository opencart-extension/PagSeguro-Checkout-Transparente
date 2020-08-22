<?php

namespace ValdeirPsr\PagSeguro\Domains\PaymentMethod;

abstract class AbstractPaymentMethod
{
    /** @var int Tipo de pagamento (Somente leitura) */
    protected $type;

    /** @var int Código de pagamento (Somente leitura) */
    protected $code;

    /** @var string Nome do método de pagamento */
    protected $method;

    /**
     * @param string $method
     */
    protected function __construct(string $method)
    {
        $this->method = $method;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }
}
