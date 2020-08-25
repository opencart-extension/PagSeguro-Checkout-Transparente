<?php

namespace ValdeirPsr\PagSeguro\Domains;

class Cart
{
    /** @var CartItem[] */
    private $items = [];

    /**
     * @param CartItem[] $values
     */
    public function __construct(array $values = [])
    {
        $this->setItems($values);
    }

    /**
     * Define os items do carrinho
     *
     * @param CartItem[] $values
     *
     * @return self
     */
    public function setItems(array $values = []): self
    {
        $this->values = [];

        foreach ($values as $value) {
            $this->addItem($value);
        }

        return $this;
    }

    /**
     * Adiciona um item ao carrinho
     *
     * @param CartItem $value
     *
     * @return self
     */
    public function addItem(CartItem $value): self
    {
        $this->items[] = $value;
        return $this;
    }

    public function getItems(): array
    {
        return $this->items;
    }
}
