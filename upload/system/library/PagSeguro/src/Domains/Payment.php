<?php

namespace ValdeirPsr\PagSeguro\Domains;

use DOMDocument;
use DOMXPath;
use ValdeirPsr\PagSeguro\Domains\{
    User\Sender
};
use ValdeirPsr\PagSeguro\Interfaces\Serializer\Xml;
use ValdeirPsr\PagSeguro\Constants\Shipping\Type;
use ValdeirPsr\PagSeguro\Parser\Xml as XmlParser;
use ValdeirPsr\PagSeguro\Domains\PaymentMethod\{
    Boleto,
    CreditCard,
    DebitCard
};

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

    /** @var AbstractPaymentMethod */
    private $payment;

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
        return $this->mode;
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
        $this->extraAmount = number_format($value, 2, '.', '');

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

    /**
     * Define a configuração de pagamento
     *
     * @param AbstractPaymentMethod $value
     *
     * @return self
     */
    public function setPayment($value): self
    {
        if ($value instanceof AbstractPaymentMethod) {
            die('ok');
        }

        $this->payment = $value;

        return $this;
    }

    /**
     * @return AbstractPaymentMethod
     */
    public function getPayment()
    {
        return $this->payment;
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

        $mode = $xpath->query('/payment/mode');

        if ($mode->count() > 0) {
            $instance->mode = $mode->item(0)->textContent;
        }

        $sender = $xpath->query('/payment/sender');

        if ($sender->count() > 0) {
            $instance->sender = Sender::fromXml($dom->saveXML($sender->item(0)));
        }

        $currency = $xpath->query('/payment/currency');

        if ($currency->count() > 0) {
            $instance->currency = $currency->item(0)->textContent;
        }

        $notificationUrl = $xpath->query('/payment/notificationURL');

        if ($notificationUrl->count() > 0) {
            $instance->notificationUrl = $notificationUrl->item(0)->textContent;
        }

        $payment = $xpath->query('/payment/method');

        if ($payment->count() > 0) {
            $paymentName = strtolower($payment->item(0)->textContent);

            if ($paymentName === 'boleto') {
                $instance->payment = Boleto::fromXml($dom->saveXML());
            } elseif ($paymentName === 'eft') {
                $instance->payment = DebitCard::fromXml($dom->saveXML());
            } elseif ($paymentName === 'creditcard') {
                $instance->payment = CreditCard::fromXml($dom->saveXML());
            }
        }

        $items = $xpath->query('/payment/items/item');

        if ($items->count() > 0) {
            foreach ($items as $item) {
                $cartItem = CartItem::fromXml($dom->saveXML($item));
                $instance->addCartItem($cartItem);
            }
        }

        $extraAmount = $xpath->query('/payment/extraAmount');

        if ($extraAmount->count() > 0) {
            $instance->extraAmount = floatval($extraAmount->item(0)->nodeValue);
        }

        $reference = $xpath->query('/payment/reference');

        if ($reference->count() > 0) {
            $instance->reference = $reference->item(0)->nodeValue;
        }

        $shipping = $xpath->query('/payment/shipping');

        if ($shipping->count() > 0) {
            $instance->shipping = Shipping::fromXml($dom->saveXML($shipping->item(0)));
        }

        $creditCard = $xpath->query('/payment/creditCard');

        if ($creditCard->count() > 0) {
            $instance->creditCard = creditCard::fromXml($dom->saveXML($shipping->item(0)));
        }

        return $instance;
    }

    /**
     * {@inheritDoc}
     */
    public function toXml()
    {
        $arr = [];

        if ($this->mode) {
            $arr['mode'] = $this->mode;
        }

        if ($this->payment) {
            $arr['method'] = $this->payment->getMethod();
        }

        if ($this->sender) {
            $arr['sender'] = $this->sender->toArray();
        }

        if ($this->currency) {
            $arr['currency'] = $this->currency;
        }

        if ($this->notificationUrl) {
            $arr['notificationURL'] = $this->notificationUrl;
        }

        if ($this->items) {
            $arr['items'] = array_map(function ($item) {
                return $item->toArray();
            }, $this->items);
        }

        if ($this->extraAmount) {
            $arr['extraAmount'] = $this->extraAmount;
        }

        if ($this->reference) {
            $arr['reference'] = $this->reference;
        }

        if ($this->shipping) {
            $arr['shipping'] = $this->shipping->toArray(true);
        }

        if ($this->payment instanceof CreditCard) {
            $arr['creditCard'] = $this->payment->toArray(true);
        }

        if ($this->payment instanceof DebitCard) {
            $arr['bank'] = [
                'name' => $this->payment->getBank()
            ];
        }

        $parser = new XmlParser();
        $result = $parser->parser([
            'payment' => $arr
        ]);

        return $result->saveXML(null);
    }
}
