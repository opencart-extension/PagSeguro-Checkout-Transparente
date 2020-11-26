<?php

require_once DIR_SYSTEM . 'library/PagSeguro/vendor/autoload.php';

use ValdeirPsr\PagSeguro\Domains\Environment;
use ValdeirPsr\PagSeguro\Domains\Transaction;
use ValdeirPsr\PagSeguro\Request\Session;
use ValdeirPsr\PagSeguro\Request\Notification;
use ValdeirPsr\PagSeguro\Domains\Logger\Logger;

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
        try {
            $env = $this->factoryEnvironment();
            $request = new Notification($env);
            $transaction = $request->capture($notification_code);
        } catch (Exception $e) {
            Logger::notice('Erro ao verificar a notificação', [
                'Code' => $notification_code
            ]);
            return null;
        }

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
     * Salva os dados no banco de dados
     *
     * @param int $order_id
     * @param Transaction $transaction
     *
     * @return void
     */
    public function addOrder(int $order_id, Transaction $transaction)
    {
        $code = $this->db->escape($transaction->getCode());
        $last_event_date = $this->db->escape($transaction->getLastEventDate()->format('Y-m-d'));

        $payment_method = $this->db->escape($transaction->getPayment()->getMethod());

        if ($payment_method === 'boleto') {
            $payment_link = $this->db->escape($transaction->getPayment()->getPaymentLink());
        } else {
            $payment_link = '';
        }

        $gross_amount = $this->db->escape($transaction->getGrossAmount());
        $discount_amount = $this->db->escape($transaction->getDiscountAmount());
        $fee_amount = $this->db->escape($transaction->getFeeAmount());
        $net_amount = $this->db->escape($transaction->getNetAmount());
        $extra_amount = $this->db->escape($transaction->getExtraAmount());
        $raw = $this->db->escape(serialize($transaction));

        $this->db->query('
            INSERT INTO ' . DB_PREFIX . 'pagseguro_orders
            VALUES (
                "' . $code . '",
                "' . $order_id . '",
                "' . $last_event_date . '",
                "' . $payment_method . '",
                "' . $payment_link . '",
                "' . $gross_amount . '",
                "' . $discount_amount . '",
                "' . $fee_amount . '",
                "' . $net_amount . '",
                "' . $extra_amount . '",
                "' . $raw . '"
            );
        ');
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

    /**
     * Captura as informações de uma transação
     *
     * @param string $order_id_or_code
     * @param array $columns `null` para todas, exceto includeRaw
     * @param bool $includeRaw
     *
     * @return array
     */
    public function getTransactionInfo($order_id_or_code, $columns = null, bool $includeRaw = false)
    {
        $columns_default = [
            'code',
            'order_id',
            'last_event_date',
            'payment_method',
            'payment_link',
            'gross_amount',
            'discount_amount',
            'fee_amount',
            'net_amount',
            'extra_amount'
        ];

        if ($columns === null && $includeRaw === true) {
            array_push($columns_default, 'raw');
        }

        if ($columns === null) {
            $columns = $columns_default;
        }

        $columns = array_map(function ($item) {
            return  (strpos($item, '.') !== false) ? $item : 'po.' . $item;
        }, $columns);

        $id = $this->db->escape($order_id_or_code);

        $query = $this->db->query('
            SELECT ' . implode(',', $columns) . '
            FROM ' . DB_PREFIX . 'pagseguro_orders po
            LEFT JOIN ' . DB_PREFIX . 'order o
                on (o.order_id = po.order_id)
            WHERE
                po.`code` = "' . $id . '"
                OR po.`order_id` = "' . $id . '"
        ');

        return $query->row;
    }
}
