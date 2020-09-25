<?php

namespace ValdeirPsr\PagSeguro\Domains\PaymentMethod;

use DOMDocument;
use DOMXPath;
use ValdeirPsr\PagSeguro\Interfaces\Serializer\Xml;
use ValdeirPsr\PagSeguro\Parser\Xml as XmlParser;

class Boleto extends AbstractPaymentMethod implements Xml
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

        return $instance;
    }

    /**
     * {@inheritDoc}
     */
    public function toXml(): string
    {
        $parser = new XmlParser();
        $result = $parser->parser(array_filter(get_object_vars($this)));

        return $result->saveXML();
    }
}
