<?php

namespace ValdeirPsr\PagSeguro\Domains;

class Error
{
    private $code;
    private $msg;

    public function __construct(string $msg, int $code = 0)
    {
        $this->code = $code;
        $this->msg = $msg;
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->msg;
    }
}
