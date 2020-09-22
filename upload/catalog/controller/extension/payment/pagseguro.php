<?php

require_once DIR_SYSTEM . 'library/PagSeguro/vendor/autoload.php';

use ValdeirPsr\PagSeguro\Domains\Logger\Logger;

class ControllerExtensionPaymentPagSeguro extends Controller
{
    const EXTENSION_PREFIX = 'payment_pagseguro_';

    public function callback()
    {
        Logger::getInstance([
            'enabled' => $this->config->get(self::EXTENSION_PREFIX . 'debug')
        ]);

        $this->load->model('extension/payment/pagseguro');
        $this->load->model('checkout/order');

        $notificationCode = $this->request->post['notificationCode'] ?? 0;

        $transaction = $this->model_extension_payment_pagseguro->checkStatusByNotificationCode($notificationCode);

        Logger::info('Notificação recebeida', [
            'code' => $notificationCode
        ]);

        if ($transaction) {
            $status = $transaction['status'];

            $statuses = [
                0 => $this->config->get(self::EXTENSION_PREFIX . 'order_status_pending'),
                1 => $this->config->get(self::EXTENSION_PREFIX . 'order_status_pending'),
                2 => $this->config->get(self::EXTENSION_PREFIX . 'order_status_analysing'),
                3 => $this->config->get(self::EXTENSION_PREFIX . 'order_status_paid'),
                4 => $this->config->get(self::EXTENSION_PREFIX . 'order_status_available'),
                5 => $this->config->get(self::EXTENSION_PREFIX . 'order_status_disputed'),
                6 => $this->config->get(self::EXTENSION_PREFIX . 'order_status_returned'),
                7 => $this->config->get(self::EXTENSION_PREFIX . 'order_status_cancelled')
            ];

            if (array_key_exists($status, $statuses)) {
                $statusId = $statuses[$status];
            } else {
                $statusId = reset($statuses);
            }

            $customer_notify = !!$this->config->get(self::EXTENSION_PREFIX . 'customer_notify');

            $this->model_checkout_order->addOrderHistory($transaction['order_id'], $statusId, '', $customer_notify);
        }
    }

    /**
     * Evento
     * trigger: catalog/view/account/order_info/before
     */
    public function boleto2(&$route, &$data)
    {
        $order_id = $this->request->get['order_id'] ?? 0;

        $this->load->model('extension/payment/pagseguro');

        $info = $this->model_extension_payment_pagseguro->getTransactionInfo($order_id, [
            'payment_link',
            'o.order_status_id'
        ]);

        if (
            isset($info['payment_link']) &&
            $this->config->get(self::EXTENSION_PREFIX . 'order_status_pending') == $info['order_status_id']
        ) {
            $data['boleto'] = $info['payment_link'];
        }
    }
}
