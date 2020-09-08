<?php

namespace ValdeirPsr\PagSeguro\Domains\PaymentMethod;

use DOMDocument;
use DOMXPath;
use ValdeirPsr\PagSeguro\Interfaces\Serializer\Xml;
use ValdeirPsr\PagSeguro\Interfaces\Serializer\IArray;
use ValdeirPsr\PagSeguro\Parser\Xml as XmlParser;
use ValdeirPsr\PagSeguro\Domains\User\Holder;
use ValdeirPsr\PagSeguro\Domains\Address;

class CreditCard extends AbstractPaymentMethod implements IArray, Xml
{
    /** @var string */
    private $token;

    /** @var int */
    private $installmentQuantity;

    /** @var float */
    private $installmentValue;

    /** @var int */
    private $noInterestInstallmentQuantity;

    /** @var Holder */
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
     * Informa o número de parcelas
     *
     * @param int $value
     *
     * @return self
     */
    public function setNoInterestInstallmentQuantity(int $value): self
    {
        $this->noInterestInstallmentQuantity = $value;

        return $this;
    }

    /**
     * @return int
     */
    public function getNoInterestInstallmentQuantity(): int
    {
        return $this->noInterestInstallmentQuantity;
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

    /**
     * {@inheritDoc}
     */
    public static function fromXml(string $xml)
    {
        $dom = new DOMDocument();
        $dom->loadXml($xml);

        $instance = new self();

        $xpath = new DOMXPath($dom);

        $type = $xpath->query('/transaction/paymentMethod/type');

        if ($type->count() > 0) {
            $instance->type = $type->item(0)->textContent;
        }

        $code = $xpath->query('/transaction/paymentMethod/code');

        if ($code->count() > 0) {
            $instance->code = $code->item(0)->textContent;
        }

        return $instance;
    }

    /**
     * {@inheritDoc}
     */
    public function toXml(): string
    {
        $parser = new XmlParser();
        $result = $parser->parser([
            'creditCard' => $this->toArray()
        ]);

        return $result->saveXML();
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return array_filter([
            'token' => $this->token,
            'installment' => array_filter([
                'quantity' => $this->installmentQuantity,
                'value' => number_format($this->installmentValue, 2, '.', ''),
                'noInterestInstallmentQuantity' => $this->noInterestInstallmentQuantity
            ]),
            'holder' => $this->holder->toArray(),
            'billingAddress' => $this->billingAddress->toArray()
        ]);
    }
}
