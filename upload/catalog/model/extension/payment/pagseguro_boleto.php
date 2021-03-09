<?php

require_once DIR_SYSTEM . 'library/PagSeguro/autoload.php';

use ValdeirPsr\PagSeguro\Domains\Logger\Logger;

class ModelExtensionPaymentPagSeguroBoleto extends Model
{
    const EXTENSION_PREFIX = 'payment_pagseguro_';

    public function getMethod($address, $total)
    {
        Logger::getInstance([
            'enabled' => $this->config->get(self::EXTENSION_PREFIX . 'debug')
        ]);

        $currencies_allowed = ['BRL'];

        if (!in_array(strtoupper($this->session->data['currency']), $currencies_allowed)) {
            Logger::debug('A moeda ' . $this->session->data['currency'] . ' não é permitida. Usar BRL');
            return [];
        }

        $this->load->language('extension/payment/pagseguro_boleto');

        $query = $this->db->query("
            SELECT * FROM `" . DB_PREFIX . "zone_to_geo_zone`
            WHERE
                geo_zone_id = '" . (int)$this->config->get(self::EXTENSION_PREFIX . 'geo_zone_id') . "'
                AND country_id = '" . (int)$address['country_id'] . "'
                AND (
                    zone_id = '" . (int)$address['zone_id'] . "'
                    OR zone_id = '0'
                )
        ");

		if (floatval($this->config->get(self::EXTENSION_PREFIX . 'methods_boleto_minimum_amount')) > $total) {
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
				'code'       => 'pagseguro_boleto',
				'title'      => $this->config->get(self::EXTENSION_PREFIX . 'methods_boleto_title'),
				'terms'      => '',
				'sort_order' => $this->config->get(self::EXTENSION_PREFIX . 'geo_sort_order')
			);
		}

		return $method_data;
    }
}
