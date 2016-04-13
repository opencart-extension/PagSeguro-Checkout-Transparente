<?php
class ControllerPaymentPagseguroBoleto extends Controller {
	
	public function index() {
	
		$data = array();
		
		$this->load->model('checkout/order');
        
        	$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        
		$this->load->model('payment/pagseguro');

		$data['session_id'] = $this->model_payment_pagseguro->captureToken();
        
	        /* CPF */
	        if (isset($order_info['custom_field'][$this->config->get('pagseguro_cpf')])) {
	            if (!preg_match('/(\.|-)/', $order_info['telephone'])) {
	                $data['cpf'] = preg_replace('/([\d]{3})([\d]{3})([\d]{3})([\d]{2})/', '$1.$2.$3-$4', $order_info['custom_field'][$this->config->get('pagseguro_cpf')]);
	            } else {
	                $data['cpf'] = $order_info['custom_field'][$this->config->get('pagseguro_cpf')];
	            }
	        } else {
	            $data['cpf'] = '';
	        }
		
		$data['continue'] = $this->url->link('checkout/success', '', 'SSL');
		
		return $this->load->view('payment/pagseguro_boleto.tpl', $data);
		
	}
	
	public function transition() {
		
		/* ID do Pedido */
		$order_id = $this->session->data['order_id'];
		
		/* Models */
		$this->load->model('checkout/order');
		$this->load->model('payment/pagseguro');
		
		/* Informações do Pedido */
		$order_info = $this->model_checkout_order->getOrder($order_id);
		
		/* Config */
		$data['email'] = $this->config->get('pagseguro_email');
		$data['token'] = $this->config->get('pagseguro_token');
		$data['paymentMode'] = 'default';
		$data['paymentMethod'] 	 = 'boleto';
		$data['currency'] = 'BRL';
		$data['notificationURL'] = $this->url->link('payment/pagseguro/callback', '', true);
		$data['reference'] = 'Pedido #' . $order_id;
		
		/* Produtos */
		$count = 1;
		
		foreach($this->cart->getProducts() as $product) {
	            if ($product['price'] > 0) {
	                $data['itemId' . $count] = $product['product_id'];
	                $data['itemDescription' . $count] = $product['name'] . ' | ' . $product['model'];
	                $data['itemAmount' . $count] = number_format($this->currency->format($product['price'], $order_info['currency_code'], $order_info['currency_value'], false), 2);
	                $data['itemQuantity' . $count] = $product['quantity'];
	                
	                $count++;
	            }
		}
        
	        /* Aplica Desconto */
	        if (isset($this->session->data['pagseguro_desconto']))
	            $data['extraAmount'] = $this->session->data['pagseguro_desconto'] * (-1);
	        
	        /* Aplica Acréscimo */
	        if (isset($this->session->data['pagseguro_acrescimo']))
	            $data['extraAmount'] += $this->session->data['pagseguro_acrescimo'];
	        
	        /* Captura desconto do cupom */
	        $discount = $this->model_payment_pagseguro->discount($order_info['total']);
	        
	        if ($discount > 0)
	            $data['extraAmount'] += ($this->model_payment_pagseguro->discount($order_info['total']) * (-1));
	        
	        /* Formata os dados */
	        if (isset($data['extraAmount']))
	            $data['extraAmount'] = number_format($data['extraAmount'], 2, '.', '');
		
		/* Nome do Cliente */
		$data['senderName'] = utf8_decode(trim($order_info['firstname']) . ' ' . trim($order_info['lastname']));
		
		/* CPF do Cliete */
		$data['senderCPF'] = preg_replace('/[^0-9]/', '', $this->request->post['cpf']);
		
		/* DDD */
		$data['senderAreaCode'] = substr(preg_replace('/[^0-9]/', '', $order_info['telephone']), 0, 2);
		
		/* Telefone do Cliente */
		$data['senderPhone'] = substr(preg_replace('/[^0-9]/', '', $order_info['telephone']), 2);
		
		/* E-mail do Cliente */
		$data['senderEmail'] = $order_info['email'];
		
		/* Hash(Identificador) da transação */
		$data['senderHash'] = $this->request->post['senderHash'];
		
		/* Endereço do Cliente */
        if (isset($this->session->data['shipping_address'])) {
            $data['shippingAddressStreet'] = utf8_decode($order_info['shipping_address_1']);
            $data['shippingAddressNumber'] = $this->model_payment_pagseguro->getAddressNumber($order_info['shipping_custom_field']);
            $data['shippingAddressDistrict'] = utf8_decode($order_info['shipping_address_2']);
            $data['shippingAddressPostalCode'] = preg_replace('/[^\d]/', '', $order_info['shipping_postcode']);
            $data['shippingAddressCity'] = utf8_decode($order_info['shipping_city']);
            $data['shippingAddressState'] = $order_info['shipping_zone_code'];
            $data['shippingAddressCountry'] = $order_info['shipping_iso_code_3'];
        } else {
            $data['shippingAddressStreet'] = utf8_decode($order_info['payment_address_1']);
            $data['shippingAddressNumber'] = $this->model_payment_pagseguro->getAddressNumber($order_info['payment_custom_field']);
            $data['shippingAddressDistrict'] = utf8_decode($order_info['payment_address_2']);
            $data['shippingAddressPostalCode'] = preg_replace('/[^\d]/', '', $order_info['payment_postcode']);
            $data['shippingAddressCity'] = utf8_decode($order_info['payment_city']);
            $data['shippingAddressState'] = $order_info['payment_zone_code'];
            $data['shippingAddressCountry'] = $order_info['payment_iso_code_3'];
        }
		
		$shipping_free = $this->model_payment_pagseguro->checkShippingFree();
		
		/* Tipo e Valor do Frete */
		if ($this->cart->hasShipping() && !$shipping_free){
			$data['shippingType'] = $this->model_payment_pagseguro->getShippingType();
		
			$data['shippingCost'] = number_format($this->session->data['shipping_method']['cost'], 2, '.', '');
		}
		
		/* Captura o retorno da requisição */
		$result = $this->model_payment_pagseguro->transition($data);
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($result));
	}
	
	public function confirm() {
		$this->load->model('checkout/order');
		
		switch ($this->request->post['status']) {
			case 1:
				$status = $this->config->get('pagseguro_aguardando_pagamento');
				break;
			case 2:
				$status = $this->config->get('pagseguro_analise');
				break;
			case 3:
				$status = $this->config->get('pagseguro_paga');
				break;
			case 4:
				$status = $this->config->get('pagseguro_disponivel');
				break;
			case 5:
				$status = $this->config->get('pagseguro_disputa');
				break;
			case 6:
				$status = $this->config->get('pagseguro_devolvida');
				break;
			case 7:
				$status = $this->config->get('pagseguro_cancelada');
				break;
			default: 
				$status = $this->config->get('pagseguro_aguardando_pagamento');
				break;
		}
		
		$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $status);
			
		if (isset($this->session->data['order_id'])) {
			$this->cart->clear();
			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['comment']);
			unset($this->session->data['coupon']);
		}
	}
}
