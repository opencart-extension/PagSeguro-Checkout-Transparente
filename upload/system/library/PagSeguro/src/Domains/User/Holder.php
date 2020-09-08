<?php

namespace ValdeirPsr\PagSeguro\Domains\User;

use DateTime;
use DOMDocument;
use DOMXPath;
use ValdeirPsr\PagSeguro\Domains\Document;
use ValdeirPsr\PagSeguro\Interfaces\Serializer\IArray;
use ValdeirPsr\PagSeguro\Interfaces\Serializer\Xml;
use ValdeirPsr\PagSeguro\Parser\Xml as XmlParser;

class Holder extends AbstractUser implements Xml, IArray
{
    /** @var DateTime */
    private $birthDate;

    /**
     * Define a data de nascimento
     *
     * @param DateTime
     *
     * @return self
     */
    public function setBirthdate(?DateTime $value): self
    {
        $this->birthDate = $value;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getBirthdate(): DateTime
    {
        return $this->birthDate;
    }

    /**
     * {@inheritDoc}
     */
    public static function fromXml(string $value)
    {
        $dom = new DOMDocument();
        $dom->loadXml($value);

        $instance = new self();

        $xpath = new DOMXpath($dom);

        $name = $xpath->query('/holder/name');

        if ($name->count() > 0) {
            $instance->name = $name->item(0)->textContent;
        }

        $documentType = $xpath->query('/holder/documents/document/type');
        $documentValue = $xpath->query('/holder/documents/document/value');

        if ($documentType->count() > 0 && $documentValue->count() > 0) {
            $documentType = strtolower($documentType->item(0)->textContent);
            $documentValue = $documentValue->item(0)->textContent;

            if ($documentType === 'cpf') {
                $instance->document = Document::cpf($documentValue);
            } elseif ($documentType === 'cnpj') {
                $instance->document = Document::cnpj($documentValue);
            }
        }

        $birthDate = $xpath->query('/holder/birthDate');

        if ($birthDate->count() > 0) {
            $instance->birthDate = DateTime::createFromFormat('d/m/Y', $birthDate->item(0)->textContent);
        }

        $phoneAreaCode = $xpath->query('/holder/phone/areaCode');
        $phoneNumber = $xpath->query('/holder/phone/number');

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
        $arr = [
            'holder' => $this->toArray()
        ];

        $parser = new XmlParser();
        $result = $parser->parser($arr);

        return $result->saveXML();
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        $arr = [
            'name' => $this->name,
            'birthDate' => $this->birthDate ? $this->birthDate->format('d/m/Y') : null,
            'documents' => [
                'document' => $this->document->toArray()
            ],
            'phone' => $this->phone
        ];

        return array_filter($arr);
    }
}
