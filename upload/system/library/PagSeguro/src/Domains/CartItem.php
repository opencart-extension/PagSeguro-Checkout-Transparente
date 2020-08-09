<?php

namespace ValdeirPsr\PagSeguro\Domains;

use ValdeirPsr\PagSeguro\Validation\Validator as v;

class CartItem
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
            throw new \InvalidArgumentException('Amount invalid. The value must have two decimal places. Was: ' . $value);
        }

        $this->amount = $value;
    }

    /**
     * @return float Retorna o preço unitário do item
     */
    public function getAmount(): float
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
}
