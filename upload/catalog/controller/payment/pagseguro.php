<?php
class ControllerPaymentPagseguro extends Controller {
	
	public function callback() {
		
		$this->load->model('payment/pagseguro');
		$this->load->model('checkout/order');
		
		$result = $this->model_payment_pagseguro->notification($this->request->post['notificationCode']);
		
		$notificar = $this->config->get('pagseguro_notificar_cliente');
		
		switch ($result['status']) {
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
				$notificar = false;
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
		
		$this->model_checkout_order->addOrderHistory($result['order_id'], $status, '', $notificar);
	}
	
}
?>