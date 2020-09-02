<?php

class ControllerExtensionPaymentPagSeguro extends Controller
{
    const EXTENSION_PREFIX = 'payment_pagseguro_';

    public function callback()
    {
        $this->load->model('extension/payment/pagseguro');
        $this->load->model('checkout/order');

        $notificationCode = $this->request->post['notificationCode'] ?? 0;

        $transaction = $this->model_extension_payment_pagseguro->checkStatusByNotificationCode($notificationCode);

        // Logger

        $status = $transaction->getStatus();

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
