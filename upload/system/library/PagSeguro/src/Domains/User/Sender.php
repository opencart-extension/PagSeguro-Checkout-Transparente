<?php

namespace ValdeirPsr\PagSeguro\Domains\User;

use DOMDocument;
use DOMXPath;
use ValdeirPsr\PagSeguro\Domains\Document;
use ValdeirPsr\PagSeguro\Interfaces\Serializer\Xml;
use ValdeirPsr\PagSeguro\Interfaces\Serializer\IArray;
use ValdeirPsr\PagSeguro\Parser\Xml as XmlParser;

class Sender extends AbstractUser implements Xml, IArray
{
    /** @var string (fingerprint) gerado pelo JavaScript do PagSeguro */
    private $hash;

    /**
     * Define o hash (fingerprint) gerado pelo JavaScript do PagSeguro
     *
     * @param string $value
     *
     * @return self
     */
    public function setHash(string $value): self
    {
        $this->hash = $value;
        return $this;
    }

    /**
     * @return string Retorna o hash (fingerprint)
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    public static function fromXml(string $value)
    {
        $dom = new DOMDocument();
        $dom->loadXml($value);

        $instance = new self();

        $xpath = new DOMXpath($dom);

        $name = $xpath->query('/sender/name');

        if ($name->count() > 0) {
            $instance->name = $name->item(0)->textContent;
        }

        $email = $xpath->query('/sender/email');

        if ($email->count() > 0) {
            $instance->email = $email->item(0)->textContent;
        }

        $documentType = $xpath->query('/sender/documents/document/type');
        $documentValue = $xpath->query('/sender/documents/document/value');

        if ($documentType->count() > 0 && $documentValue->count() > 0) {
            $documentType = strtolower($documentType->item(0)->textContent);
            $documentValue = $documentValue->item(0)->textContent;

            if ($documentType === 'cpf') {
                $instance->document = Document::cpf($documentValue);
            } elseif ($documentType === 'cnpj') {
                $instance->document = Document::cnpj($documentValue);
            }
        }

        $hash = $xpath->query('/sender/hash');

        if ($hash->count() > 0) {
            $instance->hash = $hash->item(0)->textContent;
        }

        $phoneAreaCode = $xpath->query('/sender/phone/areaCode');
        $phoneNumber = $xpath->query('/sender/phone/number');

        if ($phoneAreaCode->count() > 0 && $phoneNumber->count() > 0) {
            $instance->setPhone(
                $phoneAreaCode->item(0)->textContent,
                $phoneNumber->item(0)->textContent,
            );
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
            'sender' => $this->toArray()
        ]);

        return $result->saveXML();
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        $arr = [
            'name' => $this->name,
            'email' => $this->email,
            'hash' => $this->hash,
            'phone' => $this->phone,
            'documents' => [
                'document' => $this->document->toArray()
            ],
        ];

        return array_filter($arr);
    }
}
