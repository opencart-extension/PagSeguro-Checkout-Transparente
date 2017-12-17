<?php
class ModelExtensionPaymentPagseguroCartao extends Model {
	public function getMethod($address, $total) {
		
        /* Sub-Total (Produtos) */
        $sub_total = $this->cart->getSubTotal();
        
        /* Total (Vale-Presentes) */
        if (isset($this->session->data['vouchers'])) {
            foreach($this->session->data['vouchers'] as $voucher) {
                $sub_total += $voucher['amount'];
            }
        }
        
		if ($sub_total >= $this->config->get('payment_pagseguro_valor_minimo_debito')) {
			$this->load->language('extension/payment/pagseguro');

			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('payment_pagseguro_geo_zone') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

			if (!$this->config->get('paymen_pagseguro_geo_zone_id')) {
				$status = true;
			} elseif ($query->num_rows) {
				$status = true;
			} else {
				$status = false;
			}

			$method_data = array();

			if ($status) {
				$method_data = array(
					'code'       => 'pagseguro_cartao',
					'title'      => $this->language->get('text_cartao'),
					'terms'      => '',
					'sort_order' => $this->config->get('payment_pagseguro_sort_order')
				);
			}

			return $method_data;
		}
	}
}