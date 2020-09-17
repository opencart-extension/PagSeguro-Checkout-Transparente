<?php

namespace ValdeirPsr\PagSeguro\Domains\PaymentMethod;

use DOMDocument;
use DOMXPath;
use ValdeirPsr\PagSeguro\Interfaces\Serializer\Xml;
use ValdeirPsr\PagSeguro\Parser\Xml as XmlParser;

class DebitCard extends AbstractPaymentMethod implements Xml
{
    /** @var string Link de pagamento (somente leitura) */
    private $paymentLink;

    /** @var string ID da instituição financeira*/
    private $bank;

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

    /**
     * Define o nome da instituição financeira
     *
     * @param string $value
     *
     * @return self
     */
    public function setBank(string $value): self
    {
        $this->bank = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getBank(): string
    {
        return $this->bank;
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

        $paymentLink = $xpath->query('//paymentLink');

        if ($paymentLink->count() > 0) {
            $instance->paymentLink = trim($paymentLink->item(0)->textContent);
        }

        $bankName = $xpath->query('//bank/name');

        if ($bankName->count() > 0) {
            $instance->bank = trim($bankName->item(0)->textContent);
        }

        return $instance;
    }

    /**
     * {@inheritDoc}
     */
    public function toXml(): string
    {
        $arr = get_object_vars($this);

        if ($this->bank) {
            $arr['bank'] = [
                'name' => $this->bank
            ];
        }

        $parser = new XmlParser();
        $result = $parser->parser([
            'eft' => array_filter($arr)
        ]);

        return $result->saveXML();
    }
}
