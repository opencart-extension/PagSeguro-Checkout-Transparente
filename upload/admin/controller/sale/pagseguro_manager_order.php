<?php

require_once DIR_SYSTEM . 'library/PagSeguro/vendor/autoload.php';

use ValdeirPsr\PagSeguro\Domains\Environment;
use ValdeirPsr\PagSeguro\Request\Sale;

class ControllerSalePagSeguroManagerOrder extends Controller
{
    const EXTENSION_PREFIX = 'payment_pagseguro_';

    public function cancel()
    {
        $this->load->language('extension/payment/pagseguro');

        $order_id = $this->request->get['order_id'] ?? 0;

        $this->load->model('extension/payment/pagseguro');
        $this->load->model('sale/order');

        $transaction_info = $this->model_extension_payment_pagseguro->getTransactionInfo(
            $order_id,
            ['code']
        );

        if (!isset($transaction_info['code'])) {
            header('HTTP/1.0 404 Not Found');
            return;
        }

        try {
            $request = new Sale($this->buildEnv());
            $result = $request->void($transaction_info['code']);

            if ($result) {
                $this->session->data['pagseguro_success'] = $this->language->get('text_void_success');
                $order_status_id = $this->config->get(self::EXTENSION_PREFIX . 'order_status_cancelled');
                $this->db->escape('UPDATE ' . DB_PREFIX . 'order SET order_status_id = "' . $order_status_id . '" WHERE order_id = "' . intval($order_id) . '"');
            } else {
                $this->session->data['pagseguro_failed'] = $this->language->get('text_void_failed');
            }
        } catch (Exception $e) {
            $this->session->data['pagseguro_failed'] = $this->language->get('text_void_failed');
        }

        $this->response->redirect(
            $this->url->link(
                'sale/order/info',
                'order_id=' . $order_id .
                '&user_token=' . $this->session->data['user_token']
            )
        );
    }

    /**
     * Cria um ambiente de desenvolvimento
     *
     * @return Environment
     */
    private function buildEnv()
    {
        $email = $this->config->get(self::EXTENSION_PREFIX . 'email');
        $token = $this->config->get(self::EXTENSION_PREFIX . 'token');
        $sandbox = $this->config->get(self::EXTENSION_PREFIX . 'sandbox');

        if ($sandbox) {
            return Environment::sandbox($email, $token);
        } else {
            return Environment::production($email, $token);
        }
    }
}
