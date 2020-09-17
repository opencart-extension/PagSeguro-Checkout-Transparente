<?php

declare(strict_types=1);

namespace ValdeirPsr\PagSeguro\Domains;

use DOMDocument;
use ValdeirPsr\PagSeguro\Interfaces\Serializer\Xml;
use ValdeirPsr\PagSeguro\Interfaces\Serializer\IArray;
use ValdeirPsr\PagSeguro\Parser\Xml as XmlParser;

/**
 * Classe responsável pelo endereço de envio e entrega
 */
class Address implements Xml, IArray
{
    private $street;
    private $number;
    private $complement;
    private $district;
    private $city;
    private $state;
    private $country = 'BRA';
    private $postalCode;

    /**
     * @param string $street
     * @param string $number
     * @param string $district
     * @param string $city
     * @param string $state
     * @param string $postalCode
     * @param string|null $complement
     */
    public function __construct(
        string $street = null,
        string $number = null,
        string $district = null,
        string $city = null,
        string $state = null,
        string $postalCode = null,
        string $complement = null
    ) {
        if ($street) {
            $this->setStreet($street);
        }

        if ($number) {
            $this->setNumber($number);
        }

        if ($district) {
            $this->setDistrict($district);
        }

        if ($city) {
            $this->setCity($city);
        }

        if ($state) {
            $this->setState($state);
        }

        if ($postalCode) {
            $this->setPostalCode($postalCode);
        }

        if ($complement) {
            $this->setComplement($complement);
        }
    }

    /**
     * Define o nome da rua
     *
     * @param string $value
     *
     * @return self
     */
    public function setStreet(string $value): self
    {
        $this->street = $value;
        return $this;
    }

    /**
     * Retorna o nome da rua
     *
     * @return string
     */
    public function getStreet(): string
    {
        return $this->street;
    }

    /**
     * Define o número de endereço
     *
     * @param string $value
     *
     * @return self
     */
    public function setNumber(string $value): self
    {
        $this->number = $value;
        return $this;
    }

    /**
     * Retorna o número de endereço
     *
     * @return string
     */
    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * Define o complemento do endereço
     *
     * @param string|null $value
     */
    public function setComplement(?string $value): self
    {
        $this->complement = $value;
        return $this;
    }

    /**
     * Retorna o complemento
     *
     * @return string|null
     */
    public function getComplement(): ?string
    {
        return $this->complement;
    }


    /**
     * Define o bairro
     *
     * @param string $value
     *
     * @return self
     */
    public function setDistrict(string $value): self
    {
        $this->district = $value;
        return $this;
    }

    /**
     * Retorna o bairro
     *
     * @return string
     */
    public function getDistrict(): string
    {
        return $this->district;
    }

    /**
     * Define a cidade
     *
     * @param string $value
     *
     * @return self
     */
    public function setCity(string $value): self
    {
        $this->city = $value;
        return $this;
    }

    /**
     * Retorna a cidade informada
     *
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * Define o Estado
     *
     * @param string $value
     *
     * @return self
     */
    public function setState(string $value): self
    {
        $this->state = $value;
        return $this;
    }

    /**
     * Retorna o Estado
     *
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * Retorna o País
     *
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }


    /**
     * Define o código postal (CEP)
     *
     * @param stringi $value
     */
    public function setPostalCode(string $value): self
    {
        $this->postalCode = preg_replace('/\D/', '', $value);
        return $this;
    }

    /**
     * Retorna o CEP
     *
     * @return string (Apenas o número)
     */
    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    /**
     * {@inheritDoc}
     */
    public static function fromXml(string $value)
    {
        $dom = new DOMDocument();
        $dom->loadXML($value);

        $instance = new self();

        $street = $dom->getElementsByTagName('street');

        if ($street->count() > 0) {
            $instance->street = $street->item(0)->textContent;
        }

        $number = $dom->getElementsByTagName('number');

        if ($number->count() > 0) {
            $instance->number = $number->item(0)->textContent;
        }

        $district = $dom->getElementsByTagName('district');

        if ($district->count() > 0) {
            $instance->district = $district->item(0)->textContent;
        }

        $city = $dom->getElementsByTagName('city');

        if ($city->count() > 0) {
            $instance->city = $city->item(0)->textContent;
        }

        $state = $dom->getElementsByTagName('state');

        if ($state->count() > 0) {
            $instance->state = $state->item(0)->textContent;
        }

        $country = $dom->getElementsByTagName('country');

        if ($country->count() > 0) {
            $instance->country = $country->item(0)->textContent;
        }

        $postalCode = $dom->getElementsByTagName('postalCode');

        if ($postalCode->count() > 0) {
            $instance->postalCode = $postalCode->item(0)->textContent;
        }

        $complement = $dom->getElementsByTagName('complement');

        if ($complement->count() > 0) {
            $instance->complement = $complement->item(0)->textContent;
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
            'address' => $this->toArray()
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
