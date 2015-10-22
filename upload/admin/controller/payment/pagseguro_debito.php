<?php
class ControllerPaymentPagseguroDebito extends Controller {
	
	public function index() {
		$this->response->redirect($this->url->link('payment/pagseguro', 'token=' . $this->session->data['session'], 'SSL'));
	}
}