<?php

class ModelExtensionTotalPagSeguroDiscount extends Model
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
            $this->load->language('extension/total/pagseguro_discount', 'pg-discount');

            $discount_value = $this->getDiscountValue();

            if ($discount_value > 0) {
                $discount_total = ($discount_value / 100) * $this->cart->getSubTotal();

                $language = $this->language->get('pg-discount');

                $total['totals'][] = array(
                    'code'       => 'pagseguro_discount',
                    'title'      => $language->get('heading_title'),
                    'value'      => -$discount_total,
                    'sort_order' => $this->config->get('total_sub_total_sort_order') + 1
                );

                $total['total'] -= $discount_total;
            }
        }
    }

    /**
     * Captura o valor do desconto
     *
     * @return float
     */
    private function getDiscountValue(): float
    {
        switch ($this->session->data['payment_method']['code']) {
            case self::EXTENSION_PAGSEGURO_BOLETO:
                return floatval($this->config->get(self::EXTENSION_PAYMENT_PREFIX . 'discount_boleto'));
            break;
            case self::EXTENSION_PAGSEGURO_CREDIT:
                return floatval($this->config->get(self::EXTENSION_PAYMENT_PREFIX . 'discount_credit'));
            break;
            case self::EXTENSION_PAGSEGURO_DEBIT:
                return floatval($this->config->get(self::EXTENSION_PAYMENT_PREFIX . 'discount_debit'));
            default:
                return 0;
        }
    }
}
