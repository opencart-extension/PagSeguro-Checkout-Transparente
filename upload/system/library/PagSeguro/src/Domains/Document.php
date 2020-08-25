<?php

namespace ValdeirPsr\PagSeguro\Domains;

use DOMDocument;
use ValdeirPsr\PagSeguro\Interfaces\Serializer\Xml;
use ValdeirPsr\PagSeguro\Interfaces\Serializer\IArray;
use ValdeirPsr\PagSeguro\Parser\Xml as XmlParser;
use ValdeirPsr\PagSeguro\Validation\Validator as v;

class Document implements Xml, IArray
{
    private $type;
    private $value;

    /**
     * @param string $type CPF ou CNPJ
     * @param string $value Número do documento
     */
    private function __construct(string $type, string $value)
    {
        $this->type = strtoupper($type);
        $this->value = $value;
    }

    /**
     * Instancia a classe com o tipo CPF
     *
     * @param string $value
     *
     * @throws \InvalidArgumentException Quando o CPF for inválido
     *
     * @return self
     */
    public static function cpf(string $value): self
    {
        if (!v::cpf()->validate($value)) {
            throw new \InvalidArgumentException('CPF "' . $value . '" is invalid.', 2010);
        }

        $instance = new self('cpf', preg_replace('/\D/', '', $value));
        return $instance;
    }

    /**
     * Instancia a classe com o tipo CNPJ
     *
     * @param string $value
     *
     * @throws \InvalidArgumentException Quando o CNPJ for inválido
     *
     * @return self
     */
    public static function cnpj(string $value): self
    {
        if (!v::cnpj()->validate($value)) {
            throw new \InvalidArgumentException('CNPJ "' . $value . '" is invalid.', 2011);
        }

        $instance = new self('cnpj', preg_replace('/\D/', '', $value));
        return $instance;
    }

    /**
     * @return string Retorna o tipo de documento (CPF/CNPJ)
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string Retorna o valor do documento
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * {@inheritDoc}
     */
    public static function fromXml(string $value)
    {
        $dom = new DOMDocument();
        $dom->loadXml($value);

        $type = $dom->getElementsByTagName('type');
        $value = $dom->getElementsByTagName('value');

        if ($type->count() > 0) {
            $type = strtolower(trim($type->item(0)->textContent));
            $value = preg_replace('/\D/', '', (trim($value->item(0)->textContent)));

            if ($type === 'cpf') {
                return new self('cpf', $value);
            } elseif ($type === 'cnpj') {
                return new self('cnpj', $value);
            }
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function toXml(): string
    {
        $parser = new XmlParser();
        $result = $parser->parser([
            "document" => get_object_vars($this)
        ]);

        return $result->saveXml();
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return array_filter(get_object_vars($this));
    }
}
