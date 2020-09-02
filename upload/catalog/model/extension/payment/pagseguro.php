<?php

require_once DIR_SYSTEM . 'library/PagSeguro/vendor/autoload.php';

use ValdeirPsr\PagSeguro\Domains\Environment;
use ValdeirPsr\PagSeguro\Request\Session;
use ValdeirPsr\PagSeguro\Request\Notification;

class ModelExtensionPaymentPagSeguro extends Model
{
    const EXTENSION_PREFIX = 'payment_pagseguro_';

    public function getMethod($address, $total)
    {
        return [];
    }

    /**
     * Captura os campos personalizados
     *
     * @return array
     */
    public function getCustomFieldValues(int $order_id)
    {
        $query = $this->db->query('SELECT custom_field FROM ' . DB_PREFIX . 'order WHERE order_id = ' . $order_id);

        return json_decode($query->row['custom_field'], true);
    }

    /**
     * Gera uma nova sessão para o pedido
     *
     * @return string
     */
    public function generateSession(): string
    {
        $env = $this->factoryEnvironment();
        $session = new Session($env);
        return $session->generate();
    }

    /**
     * Captura o status do pedido, de acordo com o código da notificação
     *
     * @param string $notification_code
     *
     * @return array|null
     */
    public function checkStatusByNotificationCode(string $notification_code): ?array
    {
        $env = $this->factoryEnvironment();
        $request = new Notification($env);
        $transaction = $request->capture($notification_code);

        $order = $this->db->query('
        SELECT
            order_id
        FROM ' . DB_PREFIX . 'pagseguro_orders
        WHERE `code` = "' . $this->db->escape($transaction->getCode()) . '"
        ');

        if (isset($order->row['order_id'])) {
            return [
                "order_id" => $order->row['order_id'],
                "status" => $transaction->getStatus()
            ];
        }

        return null;
    }

    /**
     * Captura o valor do frete
     *
     * @return float
     */
    public function getShippingCost($totals): float
    {
        $result = array_filter($totals, function ($item) {
            return $item['code'] === 'shipping';
        });

        $result = array_reduce($result, function ($sum, $item) {
            return $sum += $item['value'];
        }, 0);

        return floatval($result);
    }

    /**
     * Captura os valores extras
     *
     * @return float
     */
    public function getExtraAmount($totals): float
    {
        $result = array_filter($totals, function ($item) {
            return !in_array($item['code'], ['sub_total', 'shipping', 'total']);
        });

        $result = array_reduce($result, function ($sum, $item) {
            return $sum += $item['value'];
        }, 0);

        return floatval($result);
    }

    /**
     * Instancia um objeto do tipo Environment
     * conforme a configuração de sandbox.
     *
     * @return Environment
     */
    public function factoryEnvironment(): Environment
    {
        $email = $this->config->get(self::EXTENSION_PREFIX . 'email');
        $token = $this->config->get(self::EXTENSION_PREFIX . 'token');

        if ($this->config->get(self::EXTENSION_PREFIX . 'sandbox')) {
            return Environment::sandbox($email, $token);
        }

        return Environment::production($email, $token);
    }
}
