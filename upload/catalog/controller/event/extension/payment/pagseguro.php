<?php

class ControllerEventExtensionPaymentPagSeguro extends Controller
{
    const EXTENSION_PREFIX = 'payment_pagseguro_';

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
