<?php

namespace ValdeirPsr\PagSeguro\Domains;

class Payment
{
    /** @var string */
    private $mode;

    /** @var Sender */
    private $sender;

    /** @var string */
    private $currency;

    /** @var string */
    private $notificationUrl;

    /** @var CartItem[] */
    private $items = [];

    /** @var float */
    private $extraAmount;

    /** @var string */
    private $reference;

    /** @var Shipping */
    private $shipping;

    /**
     * Define os modo
     * 
     * @param string $value
     * 
     * @return self
     */
    public function setMode(string $value): self
    {
        $this->mode = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getMode(): string
    {
        return $this->model;
    }

    /**
     * Define os dados do comprador
     * 
     * @param Sender $value
     * 
     * @return self
     */
    public function setSender(Sender $value): self
    {
        $this->sender = $value;

        return $this;
    }

    /**
     * @return Sender
     */
    public function getSender(): Sender
    {
        return $this->sender;
    }

    /**
     * Define a moeda de pagamento
     * 
     * @param string $value
     * 
     * @return self
     */
    public function setCurrency(string $value): self
    {
        $this->currency = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * Define a URL de notificação
     * 
     * @param string $value
     * 
     * @return self
     */
    public function setNotificationUrl(string $value): self
    {
        $this->notificationUrl = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getNotificationUrl(): string
    {
        return $this->notificationUrl;
    }

    /**
     * Define os items comprados
     * 
     * @param CartItem[] $values
     * 
     * @return self
     */
    public function setCartItems(array $values = []): self
    {
        $this->items = [];

        foreach ($values as $value) {
            $this->addCartItem($value);
        }

        return $this;
    }

    /**
     * Adiciona um item aos itens comprados
     * 
     * @param CartItem $value
     * 
     * @return self
     */
    public function addCartItem(CartItem $value): self
    {
        $this->items[] = $value;

        return $this;
    }

    /**
     * @return CartItem[]
     */
    public function getCartItems(): array
    {
        return $this->items;
    }

    /**
     * Define o valores extras:
     *  - valor positivo para acŕescimo;
     *  - valor negativo para desconto
     * 
     * @param float $value
     * 
     * @return self
     */
    public function setExtraAmount(float $value): self
    {
        $this->extraAmount = $value;

        return $this;
    }

    /**
     * @return float
     */
    public function getExtraAmount(): float
    {
        return $this->extraAmount;
    }

    /**
     * Define referencia para o pedidos, como ID do pedido
     * 
     * @param string $value
     * 
     * @return self
     */
    public function setReference(string $value): self
    {
        $this->reference = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getReference(): string
    {
        return $this->reference;
    }

    /**
     * Define os dados de entrega
     * 
     * @param Shipping $value
     * 
     * @return self
     */
    public function setShipping(Shipping $value): self
    {
        $this->shipping = $value;

        return $this;
    }

    /**
     * @return Shipping
     */
    public function getShipping(): Shipping
    {
        return $this->shipping;
    }
}
