<?php

namespace ValdeirPsr\PagSeguro\Interfaces\Serializer;

interface Xml
{
    public static function fromXml(string $value);

    public function toXml(): string;
}
