<?php

class ControllerExtensionPaymentPagseguro extends Controller
{
  public function index()
  {
    $data = $this->load->language('extension/payment/pagseguro');

    $this->document->setTitle($this->language->get('heading_title'));

    $data['header'] = $this->load->controller('common/header');
    $data['column_left'] = $this->load->controller('common/column_left');
    $data['footer'] = $this->load->controller('common/footer');

    $this->response->setOutput($this->load->view('extension/payment/pagseguro', $data)); 
  }
}