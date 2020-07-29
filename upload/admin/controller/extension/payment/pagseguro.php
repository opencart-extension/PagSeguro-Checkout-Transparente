<?php

class ControllerExtensionPaymentPagseguro extends Controller
{
  const FIELD_PREFIX = 'payment_pagseguro_';

  private $error = [];

  public function index()
  {
    $data = $this->load->language('extension/payment/pagseguro');

    $this->document->setTitle($this->language->get('heading_title'));

    if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {
      var_dump($this->request->post);
      die();
    }

    $data['breadcrumbs'] = [];

    $data['breadcrumbs'][] = [
      'text' => $this->language->get('text_home'),
      'href' => $this->buildUrl('common/dashboard')
    ];

    $data['breadcrumbs'][] = [
      'text' => $this->language->get('text_extension'),
      'href' => $this->buildUrl('marketplace/extension')
    ];

    $data['breadcrumbs'][] = [
      'text' => $this->language->get('heading_title'),
      'href' => ''
    ];

    foreach($this->error as $key => $value) {
      $data['error_' . $key] = $value;
    }

    $data['action'] = $this->buildUrl('extension/payment/pagseguro');
    $data['cancel'] = $this->buildUrl('marketplace/extension', [
      'type' => 'payment'
    ]);

    $data['header'] = $this->load->controller('common/header');
    $data['column_left'] = $this->load->controller('common/column_left');
    $data['footer'] = $this->load->controller('common/footer');

    $this->response->setOutput($this->load->view('extension/payment/pagseguro', $data)); 
  }

  private function validate()
  {
    $required_fields = [
      'status',
      'email',
      'token',
      'sandbox',
      'customer_notify',
      'callback_token',
      'order_status_pending',
      'order_status_analysing',
      'order_status_paid',
      'order_status_available',
      'order_status_disputed',
      'order_status_returned',
      'order_status_cancelled',
      'installment_total',
      'installment_free',
      'installment_minimum_value',
      'layout'
    ];

    if (!$this->user->hasPermission('modify', 'extension/payment/pagseguro')) {
			$this->error['warning'] = $this->language->get('error_permission');
    }
    
    foreach($required_fields as $field) {
      if (empty($this->request->post[$field])) {
        $this->error[$field] = $this->language->get('error_required');
      }
    }

    if (filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
      $this->error['email'] = $this->language->get('error_email');
    }

    if (filter_var($this->request->post['installment_total'], FILTER_VALIDATE_INT, [
      'options' => [
        'min_range' => 1,
        'max_range' => 18,
      ]
    ])) {
      $this->error['installment_total'] = $this->language->get('error_installment_total');
    }

    return !$this->error;
  }

  /**
   * Cria a URL através da rota
   * 
   * @param string $route
   * @return string
   */
  private function buildUrl(string $route, array $params = [])
  {
    $params = array_merge($params, [
      'user_token' => $this->session->data['user_token']
    ]);

    $params = array_map(function($key, $value) {
      return "$key=$value";
    }, array_keys($params), array_values($params));

    return $this->url->link($route, implode('&', $params), true);
  }
}