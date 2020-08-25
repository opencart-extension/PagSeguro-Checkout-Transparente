<?php

namespace ValdeirPsr\PagSeguro\Parser;

use DOMDocument;
use DOMElement;

class Xml
{
    private $dom;

    public function __construct(string $version = '1.0', string $encoding = 'utf-8')
    {
        $this->dom = new DOMDocument($version, $encoding);
    }

    /**
     * Transforma um array num elemento DOM
     *
     * @param array
     *
     * @return DOMDocument|null
     */
    public function parser(array $arr)
    {
        $dom = null;

        if (count($arr) > 1) {
            $dom = $this->dom;
            $elementRoot = $this->dom->appendChild(new DOMElement('root'));
            $this->dom = $elementRoot;
        }

        $this->build($arr);
        return $dom ?? $this->dom;
    }

    private function build($arr, &$parent = null)
    {
        foreach ($arr as $key => $value) {
            $newDomDocument = $parent ?? $this->dom;
            $element = null;

            if (is_numeric($key)) {
                $key = 'item';
            }

            if (is_array($value)) {
                $element = $this->dom->createElement($key);
                $this->build($value, $element);
            } else {
                $element = new DOMElement($key, $value);
            }

            $newDomDocument->appendChild($element);
        }

        return $this->dom;
    }
}
