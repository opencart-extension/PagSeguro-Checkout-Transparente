<?php

require_once DIR_SYSTEM . 'library/PagSeguro/vendor/autoload.php';

use ValdeirPsr\PagSeguro\Domains\Environment;
use ValdeirPsr\PagSeguro\Request\Session;

class ModelExtensionPaymentPagSeguro extends Model
{
    const EXTENSION_PREFIX = 'payment_pagseguro_';

    public function getMethod($address, $total)
    {
        return [];
    }

    /**
     * Gera uma nova sessão para o pedido
     */
    public function generateSession(): string
    {

    }

    private function factoryEnvironment(): Environment
    {
        $email = $this->config->get(self::EXTENSION_PREFIX . 'email');
        $token = $this->config->get(self::EXTENSION_PREFIX . 'token');

        if ($this->config->get(self::EXTENSION_PREFIX . 'sandbox')) {
            return Environment::sandbox($email, $token);
        }

        return Environment::production($email, $token);
    }
}
