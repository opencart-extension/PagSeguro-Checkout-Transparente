<?php

class ControllerExtensionPaymentPagseguro extends Controller
{
  public function index()
  {
    $data = $this->load->language('extension/payment/pagseguro');

    $this->document->setTitle($this->language->get('heading_title'));

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

    $data['action'] = $this->buildUrl('extension/payment/pagseguro');
    $data['cancel'] = $this->buildUrl('marketplace/extension', [
      'type' => 'payment'
    ]);

    $data['header'] = $this->load->controller('common/header');
    $data['column_left'] = $this->load->controller('common/column_left');
    $data['footer'] = $this->load->controller('common/footer');

    $this->response->setOutput($this->load->view('extension/payment/pagseguro', $data)); 
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