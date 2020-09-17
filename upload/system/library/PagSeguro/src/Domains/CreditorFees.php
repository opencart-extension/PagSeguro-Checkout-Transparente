<?php

namespace ValdeirPsr\PagSeguro\Domains;

use DOMDocument;
use DOMXPath;
use ValdeirPsr\PagSeguro\Interfaces\Serializer\Xml;
use ValdeirPsr\PagSeguro\Interfaces\Serializer\IArray;
use ValdeirPsr\PagSeguro\Parser\Xml as XmlParser;

class CreditorFees implements Xml, IArray
{
    /** @var float */
    private $installmentFeeAmount;

    /** @var float */
    private $intermediationRateAmount;

    /** @var float */
    private $intermediationFeeAmount;

    /**
     * @return float
     */
    public function getInstallmentFeeAmount(): ?float
    {
        return $this->installmentFeeAmount;
    }

    /**
     * @return float
     */
    public function getIntermediationRateAmount(): ?float
    {
        return $this->intermediationRateAmount;
    }

    /**
     * @return float
     */
    public function getIntermediationFeeAmount(): ?float
    {
        return $this->intermediationFeeAmount;
    }

    /**
     * {@inheritDoc}
     */
    public static function fromXml(string $value)
    {
        $dom = new DOMDocument();
        $dom->loadXml($value);

        $instance = new self();

        $xpath = new DOMXPath($dom);

        $installmentFeeAmount = $xpath->query('//installmentFeeAmount');

        if ($installmentFeeAmount->count() > 0) {
            $instance->installmentFeeAmount = $installmentFeeAmount->item(0)->textContent;
        }

        $intermediationRateAmount = $xpath->query('//intermediationRateAmount');

        if ($intermediationRateAmount->count() > 0) {
            $instance->intermediationRateAmount = $intermediationRateAmount->item(0)->textContent;
        }

        $intermediationFeeAmount = $xpath->query('//intermediationFeeAmount');

        if ($intermediationFeeAmount->count() > 0) {
            $instance->intermediationFeeAmount = $intermediationFeeAmount->item(0)->textContent;
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
            'creditorFees' => $this->toArray()
        ]);

        return $result->saveXML();
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return array_filter(get_object_vars($this));
    }
}
