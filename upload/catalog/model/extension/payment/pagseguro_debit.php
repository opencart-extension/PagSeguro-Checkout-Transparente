<?php

class ModelExtensionPaymentPagSeguroDebit extends Model
{
    const EXTENSION_PREFIX = 'payment_pagseguro_';

    public function getMethod($address, $total)
    {
        $this->load->language('extension/payment/pagseguro_debit');

        $query = $this->db->query("
            SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone
            WHERE
                geo_zone_id = '" . (int)$this->config->get(self::EXTENSION_PREFIX . 'geo_zone_id') . "'
                AND country_id = '" . (int)$address['country_id'] . "'
                AND (
                    zone_id = '" . (int)$address['zone_id'] . "'
                    OR zone_id = '0'
                )
        ");

        if (floatval($this->config->get(self::EXTENSION_PREFIX . 'methods_debit_minimum_amount')) > $total) {
            return [];
        }

        if (!$this->config->get(self::EXTENSION_PREFIX . 'geo_zone_id')) {
            $status = true;
        } elseif ($query->num_rows) {
            $status = true;
        } else {
            $status = false;
        }

        $method_data = array();

        if ($status) {
            $method_data = array(
                'code'       => 'pagseguro_debit',
                'title'      => $this->language->get('heading_title'),
                'terms'      => '',
                'sort_order' => $this->config->get(self::EXTENSION_PREFIX . 'geo_sort_order')
            );
        }

        return $method_data;
    }
}
