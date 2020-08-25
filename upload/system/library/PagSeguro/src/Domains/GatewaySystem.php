<?php

namespace ValdeirPsr\PagSeguro\Domains;

use DOMDocument;
use ValdeirPsr\PagSeguro\Interfaces\Serializer\Xml;
use ValdeirPsr\PagSeguro\Parser\Xml as XmlParser;

/**
 * Somente leitura
 * Somente quando o pagamento for via cartão de crédito
 */
class GatewaySystem implements Xml
{
    /** @var string */
    private $type;

    /** @var string */
    private $authorizationCode;

    /** @var string */
    private $nsu;

    /** @var string */
    private $tid;

    /** @var string */
    private $establishmentCode;

    /** @var string */
    private $acquirerName;

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getAuthorizationCode(): string
    {
        return $this->authorizationCode;
    }

    /**
     * @return string
     */
    public function getNsu(): string
    {
        return $this->nsu;
    }

    /**
     * @return string
     */
    public function getTid(): string
    {
        return $this->tid;
    }

    /**
     * @return string
     */
    public function getEstablishmentCode(): string
    {
        return $this->establishmentCode;
    }

    /**
     * @return string
     */
    public function getAcquirerName(): string
    {
        return $this->acquirerName;
    }

    /**
     * {@inheritDoc}
     */
    public static function fromXml(string $value)
    {
        $dom = new DOMDocument();
        $dom->loadXml($value);

        $instance = new self();

        $type = $dom->getElementsByTagName('type');

        if ($type->count() > 0) {
            $instance->type = $type->item(0)->textContent;
        }

        $authorizationCode = $dom->getElementsByTagName('authorizationCode');

        if ($authorizationCode->count() > 0) {
            $instance->authorizationCode = $authorizationCode->item(0)->textContent;
        }

        $nsu = $dom->getElementsByTagName('nsu');

        if ($nsu->count() > 0) {
            $instance->nsu = $nsu->item(0)->textContent;
        }

        $tid = $dom->getElementsByTagName('tid');

        if ($tid->count() > 0) {
            $instance->tid = $tid->item(0)->textContent;
        }

        $establishmentCode = $dom->getElementsByTagName('establishmentCode');

        if ($establishmentCode->count() > 0) {
            $instance->establishmentCode = $establishmentCode->item(0)->textContent;
        }

        $acquirerName = $dom->getElementsByTagName('acquirerName');

        if ($acquirerName->count() > 0) {
            $instance->acquirerName = $acquirerName->item(0)->textContent;
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
            "GatewaySystem" => get_object_vars($this)
        ]);

        return $result->saveXml();
    }
}
