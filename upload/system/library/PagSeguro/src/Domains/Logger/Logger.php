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

    public static function getInstance(): Monolog
    {
        if (self::$instance === null) {
            self::path();

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

    public static function path()
    {
        if (!defined('DS')) {
            define('DS', DIRECTORY_SEPARATOR);
        }

        if (!defined('PAGSEGURO_LOG')) {
            define('PAGSEGURO_LOG', __DIR__ . DS . '..' . DS . '..' . DS . '..' . DS . 'log');
        }
    }
}
