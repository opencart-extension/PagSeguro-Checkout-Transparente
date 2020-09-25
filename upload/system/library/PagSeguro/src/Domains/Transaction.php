<?php

namespace ValdeirPsr\PagSeguro\Domains;

use DateTime;
use DOMDocument;
use DOMXPath;
use ValdeirPsr\PagSeguro\Constants\PaymentMethod\Methods;
use ValdeirPsr\PagSeguro\Domains\User\Sender;
use ValdeirPsr\PagSeguro\Interfaces\Serializer\Xml;
use ValdeirPsr\PagSeguro\Parser\Xml as XmlParser;
use ValdeirPsr\PagSeguro\Domains\PaymentMethod\{
    AbstractPaymentMethod,
    Boleto,
    CreditCard,
    DebitCard
};

class Transaction implements Xml
{
    /** @var DateTime */
    private $date;

    /** @var string */
    private $payment;

    /** @var string */
    private $code;

    /** @var int */
    private $type;

    /** @var int */
    private $status;

    /** @var string */
    private $cancellationSource;

    /** @var DateTime */
    private $lastEventDate;

    /** @var float */
    private $grossAmount;

    /** @var float */
    private $discountAmount;

    /** @var CreditorFees */
    private $creditorFees;

    /** @var float */
    private $feeAmount;

    /** @var float */
    private $netAmount;

    /** @var int */
    private $installmentCount;

    /** @var int */
    private $itemCount;


    /** @var string */
    private $reference;

    /** @var float */
    private $extraAmount;

    /** @var DateTime */
    private $escrowEndDate;

    /** @var CartItem[] */
    private $items = [];

    /** @var Sender */
    private $sender;

    /** @var Shipping */
    private $shipping;

    /** @var GatewaySystem */
    private $gatewaySystem;

    /**
     * @return DateTime
     */
    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    /**
     * @return AbstractPaymentMethod
     */
    public function getPayment(): ?AbstractPaymentMethod
    {
        return $this->payment;
    }

    /**
     * @return string
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @return int
     */
    public function getType(): ?int
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getCancellationSource(): ?string
    {
        return $this->cancellationSource;
    }

    /**
     * @return DateTime
     */
    public function getLastEventDate(): ?DateTime
    {
        return $this->lastEventDate;
    }

    /**
     * @return float
     */
    public function getGrossAmount(): ?float
    {
        return $this->grossAmount;
    }

    /**
     * @return float
     */
    public function getDiscountAmount(): ?float
    {
        return $this->discountAmount;
    }

    /**
     * @return CreditorFees
     */
    public function getCreditorFees(): ?CreditorFees
    {
        return $this->creditorFees;
    }

    /**
     * @return float
     */
    public function getFeeAmount(): ?float
    {
        return $this->feeAmount;
    }

    /**
     * @return float
     */
    public function getNetAmount(): ?float
    {
        return $this->netAmount;
    }

    /**
     * @return int
     */
    public function getInstallmentCount(): ?int
    {
        return $this->installmentCount;
    }

    /**
     * @return int
     */
    public function getItemCount(): ?int
    {
        return $this->itemCount;
    }

    /**
     * @return string
     */
    public function getReference(): ?string
    {
        return $this->reference;
    }

    /**
     * @return float
     */
    public function getExtraAmount(): ?float
    {
        return $this->extraAmount;
    }

    /**
     * @return DateTime
     */
    public function getEscrowEndDate(): ?DateTime
    {
        return $this->escrowEndDate;
    }

    /**
     * @return CartItem[]
     */
    public function getItems(): ?array
    {
        return $this->items;
    }

    /**
     * @return Sender
     */
    public function getSender(): ?Sender
    {
        return $this->sender;
    }

    /**
     * @return Shipping
     */
    public function getShipping(): ?Shipping
    {
        return $this->shipping;
    }

    /**
     * @return GatewaySystem
     */
    public function getGatewaySystem(): ?GatewaySystem
    {
        return $this->gatewaySystem;
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

        $date = $xpath->query('/transaction/date');

        if ($date->count() > 0) {
            $instance->date = DateTime::createFromFormat('Y-m-d\TH:i:s.000P', $date->item(0)->textContent);
        }

        $payment = $xpath->query('/transaction/paymentMethod/type');

        if ($payment->count() > 0) {
            $paymentType = intval($payment->item(0)->textContent);

            if ($paymentType === Methods::CREDITCARD) {
                $instance->payment = CreditCard::fromXml($dom->saveXML());
            } elseif ($paymentType === Methods::BOLETO) {
                $instance->payment = Boleto::fromXml($dom->saveXML());
            } elseif ($paymentType === Methods::ELETRONIC_TRANSFER) {
                $instance->payment = DebitCard::fromXml($dom->saveXML());
            }
        }

        $code = $xpath->query('/transaction/code');

        if ($code->count() > 0) {
            $instance->code = $code->item(0)->textContent;
        }

        $type = $xpath->query('/transaction/type');

        if ($type->count() > 0) {
            $instance->type = intval($type->item(0)->textContent);
        }

        $status = $xpath->query('/transaction/status');

        if ($status->count() > 0) {
            $instance->status = intval($status->item(0)->textContent);
        }

        $cancellationSource = $xpath->query('/transaction/cancellationSource');

        if ($cancellationSource->count() > 0) {
            $instance->cancellationSource = $cancellationSource->item(0)->textContent;
        }

        $creditorFees = $xpath->query('/transaction/creditorFees');

        if ($creditorFees->count() > 0) {
            $instance->creditorFees = CreditorFees::fromXml($dom->saveXML($creditorFees->item(0)));
        }

        $lastEventDate = $xpath->query('/transaction/lastEventDate');

        if ($lastEventDate->count() > 0) {
            $instance->lastEventDate = DateTime::createFromFormat(
                'Y-m-d\TH:i:s\.000P',
                $lastEventDate->item(0)->textContent
            );
        }

        $grossAmount = $xpath->query('/transaction/grossAmount');

        if ($grossAmount->count() > 0) {
            $instance->grossAmount = floatval($grossAmount->item(0)->textContent);
        }

        $discountAmount = $xpath->query('/transaction/discountAmount');

        if ($discountAmount->count() > 0) {
            $instance->discountAmount = floatval($discountAmount->item(0)->textContent);
        }

        $feeAmount = $xpath->query('/transaction/feeAmount');

        if ($feeAmount->count() > 0) {
            $instance->feeAmount = floatval($feeAmount->item(0)->textContent);
        }

        $netAmount = $xpath->query('/transaction/netAmount');

        if ($netAmount->count() > 0) {
            $instance->netAmount = floatval($netAmount->item(0)->textContent);
        }

        $installmentCount = $xpath->query('/transaction/installmentCount');

        if ($installmentCount->count() > 0) {
            $instance->installmentCount = intval($installmentCount->item(0)->textContent);
        }

        $itemCount = $xpath->query('/transaction/itemCount');

        if ($itemCount->count() > 0) {
            $instance->itemCount = intval($itemCount->item(0)->textContent);
        }

        $reference = $xpath->query('/transaction/reference');

        if ($reference->count() > 0) {
            $instance->reference = $reference->item(0)->textContent;
        }

        $extraAmount = $xpath->query('/transaction/extraAmount');

        if ($extraAmount->count() > 0) {
            $instance->extraAmount = floatval($extraAmount->item(0)->textContent);
        }

        $escrowEndDate = $xpath->query('/transaction/escrowEndDate');

        if ($escrowEndDate->count() > 0) {
            $instance->escrowEndDate = DateTime::createFromFormat(
                'Y-m-d\TH:i:s\.000P',
                $escrowEndDate->item(0)->textContent
            );
        }

        $items = $xpath->query('/transaction/items/item');

        if ($items->count() > 0) {
            foreach ($items as $item) {
                $instance->items[] = CartItem::fromXml($dom->saveXML($item));
            }
        }

        $sender = $xpath->query('/transaction/sender');

        if ($sender->count() > 0) {
            $instance->sender = Sender::fromXml($dom->saveXML($sender->item(0)));
        }

        $shipping = $xpath->query('/transaction/shipping');

        if ($shipping->count() > 0) {
            $instance->shipping = Shipping::fromXml($dom->saveXML($shipping->item(0)));
        }

        $gatewaySystem = $xpath->query('/transaction/gatewaySystem');

        if ($gatewaySystem->count() > 0) {
            $instance->gatewaySystem = GatewaySystem::fromXml($dom->saveXML($gatewaySystem->item(0)));
        }

        return $instance;
    }

    public function toXml(): string
    {
        return '';
    }
}
