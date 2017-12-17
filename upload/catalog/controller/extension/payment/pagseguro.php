<?php
class ControllerExtensionPaymentPagseguro extends Controller {
	
	public function callback() {
		
        $this->log->write($this->request->post['notificationCode']);
        
		$this->load->model('extension/payment/pagseguro');
		$this->load->model('checkout/order');
		
		$result = $this->model_extension_payment_pagseguro->notification($this->request->post['notificationCode']);
		
		$notificar = $this->config->get('pagseguro_notificar_cliente');
        
        if (is_array($result['status'])) {
            $status = reset($result['status']);
        } else {
            $status = $result['status'];
        }
		
		switch ($status) {
			case 1:
				$status = $this->config->get('payment_pagseguro_aguardando_pagamento');
				break;
			case 2:
				$status = $this->config->get('payment_pagseguro_analise');
				break;
			case 3:
				$status = $this->config->get('payment_pagseguro_paga');
				break;
			case 4:
				$status = $this->config->get('payment_pagseguro_disponivel');
				$notificar = false;
				break;
			case 5:
				$status = $this->config->get('payment_pagseguro_disputa');
				break;
			case 6:
				$status = $this->config->get('payment_pagseguro_devolvida');
				break;
			case 7:
				$status = $this->config->get('payment_pagseguro_cancelada');
				break;
			default: 
				$status = $this->config->get('payment_pagseguro_aguardando_pagamento');
				break;
		}
		
		$this->model_checkout_order->addOrderHistory($result['order_id'], $status, '', $notificar);
	}
	
}
?>