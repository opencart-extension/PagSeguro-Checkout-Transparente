<?php
class ControllerExtensionTotalPagSeguroDesconto extends Controller {
    
    public function index() {        
        $this->response->redirect($this->url->link('extension/payment/pagseguro', 'token=' . $this->session->data['token'], true));
    }
}