<?php

namespace ValdeirPsr\PagSeguro\Validation;

class Factory
{
    private const RULES_NAMESPACE = '\\ValdeirPsr\\PagSeguro\\Validation\\Rules\\';

    private static $defaultInstance;

    /**
     * Captura a intancia atual do objeto
     */
    public static function getDefaultInstance()
    {
        if (self::$defaultInstance === null) {
            self::$defaultInstance = new self();
        }

        return self::$defaultInstance;
    }

    /**
     * Instancia, caso exista, a regra informada
     *
     * @param string $name regra
     * @param string $args
     *
     * @throws \UnexpectedValueException Caso a regra não exista
     * @throws \BadMethodCallException Caso a classe da regra não seja instanciável
     *
     * @return IValidation
     */
    public function rule($name, $args)
    {
        $className = self::RULES_NAMESPACE . ucfirst($name);

        if (!class_exists($className)) {
            throw new \UnexpectedValueException("Rule {$className} not found", 2000);
        }

        $reflection = new \ReflectionClass($className);

        if (!$reflection->isInstantiable()) {
            throw new \BadMethodCallException("Class $className is not instantiable", 2001);
        }

        if ($reflection->getConstructor() !== null) {
            return $reflection->newInstanceArgs($args);
        } else {
            return $reflection->newInstanceWithoutConstructor();
        }
    }
}
