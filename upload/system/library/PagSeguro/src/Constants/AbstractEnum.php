<?php

namespace ValdeirPsr\PagSeguro\Constants;

abstract class AbstractEnum
{
    private static $enums = [];

    private function __construct()
    {
        /** Preventing instance */
    }

    /**
     * Captura a lista de constantes
     *
     * @return array
     */
    private static function getEnums(): array
    {
        if (self::$enums === null) {
            self::$enums = [];
        }

        $calledClass = get_called_class();

        if (!array_key_exists($calledClass, self::$enums)) {
            $reflect = new \ReflectionClass($calledClass);
            self::$enums[$calledClass] = $reflect->getConstants();
        }

        return self::$enums[$calledClass];
    }

    /**
     * Verifica se determinada chave existe
     *
     * @return bool
     */
    public static function has($name): bool
    {
        return array_key_exists($name, self::getEnums());
    }

    /**
     * Verifica se determinado valor existe
     *
     * @return bool
     */
    public static function isValidValue($value): bool
    {
        $values = array_values(self::getEnums());
        return in_array($value, $values);
    }
}
