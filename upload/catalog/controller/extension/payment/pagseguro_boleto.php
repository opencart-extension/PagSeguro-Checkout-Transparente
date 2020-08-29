<?php

class ControllerExtensionPaymentPagSeguroBoleto extends Controller
{
    const EXTENSION_PREFIX = 'payment_pagseguro_';

    public function index()
    {
        $data = $this->language->load('extension/payment/pagseguro_boleto');

        $this->load->model('extension/payment/pagseguro');

        $data['session'] = $this->model_extension_payment_pagseguro->generateSession();

        if ($this->config->get(self::EXTENSION_PREFIX . 'sandbox')) {
            $data['javascript_src'] = 'https://stc.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js';
        } else {
            $data['javascript_src'] = 'https://stc.sandbox.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js';
        }

        return $this->load->view('extension/payment/pagseguro_boleto', $data);
    }
}
