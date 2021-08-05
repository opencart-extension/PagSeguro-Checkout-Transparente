<?php

require_once DIR_SYSTEM . 'library/PagSeguro/autoload.php';

use ValdeirPsr\PagSeguro\Domains\Logger\Logger;
use ValdeirPsr\PagSeguro\Request\Sale;
use ValdeirPsr\PagSeguro\Domains\Environment;

class ControllerExtensionPaymentPagSeguroBoleto extends Controller
{
    const EXTENSION_PREFIX = 'payment_pagseguro_';

    public function index()
    {
        $this->response->redirect(
            $this->url->link('extension/payment/pagseguro', 'user_token=' . $this->session->data['user_token'])
        );
    }

    public function order()
    {
        $new_data = $this->load->language('extension/payment/pagseguro');

        $order_id = $this->request->get['order_id'] ?? 0;

        $order_info = $this->model_sale_order->getOrder($order_id);

        if ($order_info && strpos($order_info['payment_code'], 'pagseguro_') === 0) {

            $this->load->model('extension/payment/pagseguro');

            $data = [];

            try {
                $this->load->model('localisation/order_status');
    
    		    $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
    
                $new_data['details'] = $this->details($order_id, $order_info);
                $new_data['cancel'] = $this->cancel($order_id, $order_info, $data);
                $new_data['refund'] = $this->refund($order_id, $order_info, $data);
                $new_data['pagseguro_success'] = $this->session->data['pagseguro_success'] ?? false;
                $new_data['pagseguro_failed'] = $this->session->data['pagseguro_failed'] ?? false;
    
                if (isset($this->session->data['pagseguro_success'])) {
                    unset($this->session->data['pagseguro_success']);
                }
    
                if (isset($this->session->data['pagseguro_failed'])) {
                    unset($this->session->data['pagseguro_failed']);
                }

                return $this->load->view('extension/payment/pagseguro_manager_order', $new_data);
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }

        return '';
    }

    /**
     * Captura os detalhes da transação
     *
     * @param int $order_id
     * @param array $order_info
     *
     * @return array
     */
    private function details($order_id, $order_info)
    {
        Logger::getInstance([
            'enabled' => $this->config->get(self::EXTENSION_PREFIX . 'debug')
        ]);

        $transaction_info = $this->model_extension_payment_pagseguro->getTransactionInfo(
            $order_id,
            ['code']
        );

        if (!isset($transaction_info['code'])) {
            return;
        }

        $request = new Sale($this->buildEnv());

        $result = $request->info($transaction_info['code']);

        $payment = $result->getPayment();

        $creditor_fees_data = [];

        $creditor_fees = $result->getCreditorFees();

        if ($creditor_fees) {
            $creditor_fees_data = [
                'installmentFeeAmount' => $this->currency->format($creditor_fees->getInstallmentFeeAmount(), 'BRL'),
                'intermediationRateAmount' => $this->currency->format($creditor_fees->getIntermediationRateAmount(), 'BRL'),
                'intermediationFeeAmount' => $this->currency->format($creditor_fees->getIntermediationFeeAmount(), 'BRL')
            ];
        }

        $payment_link = false;

        if (
            $payment->getMethod() === 'boleto' &&
            $order_info['order_status_id'] == $this->config->get(self::EXTENSION_PREFIX . 'order_status_pending')
        ) {
            $payment_link = $payment->getPaymentLink();
        }

        return [
            'date' => $result->getDate()->format('F j, Y, H:i:s'),
            'code' => $result->getCode(),
            'lastEventDate' => $result->getLastEventDate()->format('F j, Y, H:i:s'),
            'grossAmount' => $this->currency->format($result->getGrossAmount(), 'BRL'),
            'discountAmount' => $this->currency->format($result->getDiscountAmount(), 'BRL'),
            'feeAmount' => $this->currency->format($result->getFeeAmount(), 'BRL'),
            'netAmount' => $this->currency->format($result->getNetAmount(), 'BRL'),
            'extraAmount' => $this->currency->format($result->getExtraAmount(), 'BRL'),
            'installmentCount' => $result->getInstallmentCount(),
            'paymentLink' => $payment_link,
            'creditorFees' => $creditor_fees_data
        ];
    }

    /**
     * Captura uma _view_ com os dados para cancelamento
     *
     * @param int $order_id
     * @param array $order_info
     * @param array $data
     *
     * @return array
     */
    private function cancel($order_id, $order_info, $data)
    {
        $status_pending = array_filter($data['order_statuses'], function ($item) {
            return $item['order_status_id'] == $this->config->get(self::EXTENSION_PREFIX . 'order_status_pending');
        });
        $status_pending = reset($status_pending);

        $status_analysing = array_filter($data['order_statuses'], function ($item) {
            return $item['order_status_id'] == $this->config->get(self::EXTENSION_PREFIX . 'order_status_analysing');
        });
        $status_analysing = reset($status_analysing);

        $cancel_availabled = in_array($order_info['order_status_id'], [
            $status_pending['order_status_id'],
            $status_analysing['order_status_id']
        ]);

        return [
            'text_alert_cancel' => sprintf($this->language->get('text_alert_cancel'), $status_pending['name'], $status_analysing['name']),
            'availabled' => $cancel_availabled,
            'url' => $this->url->link('extension/payment/pagseguro/cancel_order', 'order_id=' . $order_id)
        ];
    }

    /**
     * Captura uma _view_ com os dados para reembolso
     *
     * @param int $order_id
     * @param array $order_info
     * @param array $data
     *
     * @return array
     */
    private function refund($order_id, $order_info, $data)
    {
        $status_paid = array_filter($data['order_statuses'], function ($item) {
            return $item['order_status_id'] == $this->config->get(self::EXTENSION_PREFIX . 'order_status_paid');
        });
        $status_paid = reset($status_paid);

        $status_available = array_filter($data['order_statuses'], function ($item) {
            return $item['order_status_id'] == $this->config->get(self::EXTENSION_PREFIX . 'order_status_available');
        });
        $status_available = reset($status_available);

        $status_disputed = array_filter($data['order_statuses'], function ($item) {
            return $item['order_status_id'] == $this->config->get(self::EXTENSION_PREFIX . 'order_status_disputed');
        });
        $status_disputed = reset($status_disputed);

        $refund_availabled = in_array($order_info['order_status_id'], [
            $status_paid['order_status_id'],
            $status_available['order_status_id'],
            $status_disputed['order_status_id']
        ]);

        return [
            'text_alert_refund' => sprintf(
                $this->language->get('text_alert_refund'),
                $status_paid['name'],
                $status_available['name'],
                $status_disputed['name']
            ),
            'availabled' => $refund_availabled,
            'url' => $this->url->link('extension/payment/pagseguro/refund_order', 'order_id=' . $order_id)
        ];
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
