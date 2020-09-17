<?php

namespace ValdeirPsr\PagSeguro\Domains;

use DOMDocument;
use ValdeirPsr\PagSeguro\Interfaces\Serializer\Xml;
use ValdeirPsr\PagSeguro\Interfaces\Serializer\IArray;
use ValdeirPsr\PagSeguro\Parser\Xml as XmlParser;
use ValdeirPsr\PagSeguro\Validation\Validator as v;

class CartItem implements Xml, IArray
{
    /** @var string Identificador do produto. Deve ser único */
    private $id;

    /** @var string */
    private $description;

    /** @var float */
    private $amount;

    /** @var int */
    private $quantity;

    /**
     * Identifica o item. Você pode escolher códigos que tenham significado para
     * seu sistema e informá-los nestes parâmetros.
     *
     * @param string $value
     *
     * @return self
     */
    public function setId(string $value): self
    {
        $this->id = $value;
        return $this;
    }

    /**
     * @return string Retorna o identificador do item
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Descreve o item. A descrição é o texto que o PagSeguro mostra associado a
     * cada item quando o comprador está finalizando o pagamento, portanto é
     * importante que ela seja clara e explicativa.
     *
     * @param string $value
     *
     * @return self
     */
    public function setDescription(string $value): self
    {
        $this->description = $value;
        return $this;
    }

    /**
     * @return string Retorna a descrição do item
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Representa o preço unitário do item. Este método define o valor de uma
     * unidade do item, que será multiplicado pela quantidade para obter o valor
     * total dentro do pagamento.
     *
     * @param float $value
     *
     * @throws \InvalidArgumentException Caso o valor possua mais de duas casas decimais
     *
     * @return self
     */
    public function setAmount(float $value): self
    {
        if (!v::Money(2)->validate($value)) {
            throw new \InvalidArgumentException('Amount invalid. The value must have two decimal places. ' .
            'Was: ' . $value);
        }

        $this->amount = number_format($value, 2, '.', '');

        return $this;
    }

    /**
     * @return float Retorna o preço unitário do item
     */
    public function getAmount(): string
    {
        return $this->amount;
    }

    /**
     * Representa a quantidade comprada de determinado item. Esta função define
     * a quantidade de um item, que será multiplicado pelo valor unitário para
     * obter o valor total dentro do pagamento.
     *
     * @param int $value
     *
     * @return self
     */
    public function setQuantity(int $value): self
    {
        $this->quantity = $value;
        return $this;
    }

    /**
     * @return int Retorna a quantidade comprada
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * {@inheritDoc}
     */
    public static function fromXml(string $value)
    {
        $dom = new DOMDocument();
        $dom->loadXML($value);

        $instance = new self();

        $id = $dom->getElementsByTagName('id');

        if ($id->count() > 0) {
            $instance->id = $id->item(0)->textContent;
        }

        $description = $dom->getElementsByTagName('description');

        if ($description->count() > 0) {
            $instance->description = $description->item(0)->textContent;
        }

        $quantity = $dom->getElementsByTagName('quantity');

        if ($quantity->count() > 0) {
            $instance->quantity = $quantity->item(0)->textContent;
        }

        $amount = $dom->getElementsByTagName('amount');

        if ($amount->count() > 0) {
            $instance->amount = $amount->item(0)->textContent;
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
            'item' => $this->toArray()
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
