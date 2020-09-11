<?php

require_once DIR_SYSTEM . 'library/PagSeguro/vendor/autoload.php';

use ValdeirPsr\PagSeguro\Domains\Environment;
use ValdeirPsr\PagSeguro\Request\Sale;

class ControllerEventExtensionPaymentPagseguro extends Controller
{
    const EXTENSION_PREFIX = 'payment_pagseguro_';

    /**
     * Exibe detalhes da transação
     * Trigger: admin/view/sale/order/info/before
     *
     * @param mixed $route
     * @param mixed $data
     */
    public function manager_order(&$route, &$data)
    {
        $new_data = $this->load->language('extension/payment/pagseguro');

        $order_id = $this->request->get['order_id'] ?? 0;

        $order_info = $this->model_sale_order->getOrder($order_id);

        if ($order_info && strpos($order_info['payment_code'], 'pagseguro_') === 0) {

            $this->load->model('extension/payment/pagseguro');

            $new_data['details'] = $this->details($order_id, $order_info);

            $data['pagseguro'] = $this->load->view('sale/order_pagseguro', $new_data);
            return;
        }

        $data['pagseguro'] = '';
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
                'intermediationFeeAmount' => $this->currency->format($creditor_fees->getIntermediationFeeAmount(), 'BRL'),
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
