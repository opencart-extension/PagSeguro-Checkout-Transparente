<?php
class ControllerPaymentPagseguroBoleto extends Controller {
	
	public function index() {
		if ($this->session->data['session']){
			$this->response->redirect($this->url->link('payment/pagseguro', 'token=' . $this->session->data['session'], 'SSL'));
		}
	}
}