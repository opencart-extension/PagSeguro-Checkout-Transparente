<?php

class ModelExtensionTotalPagSeguroFee extends Model
{
    const EXTENSION_PAYMENT_PREFIX = 'payment_pagseguro_';
    const EXTENSION_PAGSEGURO_BOLETO = 'pagseguro_boleto';
    const EXTENSION_PAGSEGURO_CREDIT = 'pagseguro_credit';
    const EXTENSION_PAGSEGURO_DEBIT = 'pagseguro_zdebit';

    public function getTotal($total) {
        $status_key = self::EXTENSION_PAYMENT_PREFIX . 'status';

        if (
            isset($this->session->data['payment_method']) &&
            $this->config->get($status_key)
        ) {
            $this->load->language('extension/total/pagseguro_fee', 'pg-fee');

            $fee_value = $this->getFeeValue();

            if ($fee_value > 0) {
                $fee_total = ($fee_value / 100) * $this->cart->getSubTotal();

                $language = $this->language->get('pg-fee');

                $total['totals'][] = array(
                    'code'       => 'pagseguro_fee',
                    'title'      => $language->get('heading_title'),
                    'value'      => $fee_total,
                    'sort_order' => $this->config->get('total_sub_total_sort_order') + 1
                );

                $total['total'] += $fee_total;
            }
        }
    }

    /**
     * Captura o valor da taxa
     *
     * @return float
     */
    private function getFeeValue(): float
    {
        switch ($this->session->data['payment_method']['code']) {
            case self::EXTENSION_PAGSEGURO_BOLETO:
                return floatval($this->config->get(self::EXTENSION_PAYMENT_PREFIX . 'fee_boleto'));
            break;
            case self::EXTENSION_PAGSEGURO_CREDIT:
                return floatval($this->config->get(self::EXTENSION_PAYMENT_PREFIX . 'fee_credit'));
            break;
            case self::EXTENSION_PAGSEGURO_DEBIT:
                return floatval($this->config->get(self::EXTENSION_PAYMENT_PREFIX . 'fee_debit'));
            default:
                return 0;
        }
    }
}
