<?php

namespace ValdeirPsr\PagSeguro\Domains;

use ValdeirPsr\PagSeguro\Domains\User\Sender;
use DateTime;

class Transasction
{
    /** @var DateTime */
    private $date;

    /** @var string */
    private $code;

    /** @var int */
    private $type;

    /** @var int */
    private $status;

    /** @var DateTime */
    private $lastEventDate;

    /** @var float */
    private $grossAmount;

    /** @var float */
    private $discountAmount;

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
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return DateTime
     */
    public function getLastEventDate(): DateTime
    {
        return $this->lastEventDate;
    }

    /**
     * @return float
     */
    public function getGrossAmount(): float
    {
        return $this->grossAmount;
    }

    /**
     * @return float
     */
    public function getDiscountAmount(): float
    {
        return $this->discountAmount;
    }

    /**
     * @return float
     */
    public function getFeeAmount(): float
    {
        return $this->feeAmount;
    }

    /**
     * @return float
     */
    public function getNetAmount(): float
    {
        return $this->netAmount;
    }

    /**
     * @return int
     */
    public function getInstallmentCount(): int
    {
        return $this->installmentCode;
    }

    /**
     * @return int
     */
    public function getItemCount(): int
    {
        return $this->itemCount;
    }

    /**
     * @return string
     */
    public function getReference(): string
    {
        return $this->reference;
    }

    /**
     * @return float
     */
    public function getExtraAmount(): float
    {
        return $this->extraAmount;
    }

    /**
     * @return CartItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return Sender
     */
    public function getSender(): Sender
    {
        return $this->sender;
    }

    /**
     * @return Shipping
     */
    public function getShipping(): Shipping
    {
        return $this->shipping;
    }

    /**
     * @return GatewaySystem
     */
    public function getGatewaySystem(): GatewaySystem
    {
        return $this->gatewaySystem;
    }
}
