<?php

namespace ValdeirPsr\PagSeguro\Domains\Logger;

use Monolog\Logger as Monolog;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\HtmlFormatter;

class Logger
{
    private static $instance;

    private function __construct()
    {
        /** Previning */
    }

    /**
     * Captura uma instância criada (ou uma cria) do Monolog
     *
     * @return Monolog
     */
    public static function getInstance(): Monolog
    {
        if (self::$instance === null) {
            self::init();

            $dateFormat = "Y-m-d\TH:i:s";
            $output = "%datetime%  ::  %level_name%  ::  %message% %context% %extra%\n";
            $formatter = new HtmlFormatter($dateFormat);

            $stream = new StreamHandler(PAGSEGURO_LOG . '/' . date('Y-m-d') . '.log');
            $stream->setFormatter($formatter);

            self::$instance = new Monolog('security');
            self::$instance->pushHandler($stream);
        }

        return self::$instance;
    }

    /**
     * Escreve uma mensagem no log.
     * Level: DEBUG
     *
     * @param mixed $value
     * @param array $context
     *
     * @return bool
     */
    public static function debug($value, array $context = [])
    {
        return self::addRecord(Monolog::DEBUG, $value, $context);
    }

    /**
     * Escreve uma mensagem no log.
     * Level: INFO
     *
     * @param mixed $value
     * @param array $context
     *
     * @return bool
     */
    public static function info($value, array $context = [])
    {
        return self::addRecord(Monolog::INFO, $value, $context);
    }

    /**
     * Escreve uma mensagem no log.
     * Level: NOTICE
     *
     * @param mixed $value
     * @param array $context
     *
     * @return bool
     */
    public static function notice($value, array $context = [])
    {
        return self::addRecord(Monolog::NOTICE, $value, $context);
    }

    /**
     * Escreve uma mensagem no log.
     * Level: WARNING
     *
     * @param mixed $value
     * @param array $context
     *
     * @return bool
     */
    public static function warning($value, array $context = [])
    {
        return self::addRecord(Monolog::WARNING, $value, $context);
    }

    /**
     * Escreve uma mensagem no log.
     * Level: ERROR
     *
     * @param mixed $value
     * @param array $context
     *
     * @return bool
     */
    public static function error($value, array $context = [])
    {
        return self::addRecord(Monolog::ERROR, $value, $context);
    }

    /**
     * Escreve uma mensagem no log.
     * Level: CRITICAL
     *
     * @param mixed $value
     * @param array $context
     *
     * @return bool
     */
    public static function critical($value, array $context = [])
    {
        return self::addRecord(Monolog::CRITICAL, $value, $context);
    }

    /**
     * Escreve uma mensagem no log.
     * Level: ALERT
     *
     * @param mixed $value
     * @param array $context
     *
     * @return bool
     */
    public static function alert($value, array $context = [])
    {
        return self::addRecord(Monolog::ALERT, $value, $context);
    }

    /**
     * Escreve uma mensagem no log.
     * Level: EMERGENCY
     *
     * @param mixed $value
     * @param array $context
     *
     * @return bool
     */
    public static function emergency($value, array $context = [])
    {
        return self::addRecord(Monolog::EMERGENCY, $value, $context);
    }

    /**
     * Escreve uma mensagem no log.
     * Level: EMERGENCY
     *
     * @param mixed $value
     */
    protected static function addRecord(int $level, $message, array $context = []): bool
    {
        return (self::getInstance())->addRecord($level, (string) $message, $context);
    }

    /**
     * Captura ou cria constante com caminho do arquivo de log
     */
    private static function init()
    {
        if (!defined('DS')) {
            define('DS', DIRECTORY_SEPARATOR);
        }

        if (!defined('PAGSEGURO_LOG')) {
            define('PAGSEGURO_LOG', __DIR__ . DS . '..' . DS . '..' . DS . '..' . DS . 'log');
        }
    }
}
