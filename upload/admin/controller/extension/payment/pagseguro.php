<?php

class ControllerExtensionPaymentPagseguro extends Controller
{
    const FIELD_PREFIX = 'payment_pagseguro_';

    private $error = [];

    /**
     * Exibe o formulário de configuração do módulo
     */
    public function index()
    {
        $data = $this->load->language('extension/payment/pagseguro');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->document->addScript('https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js');

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {
            $this->load->model('setting/setting');

            $keys = array_map(function($key) {
                return self::FIELD_PREFIX . $key;
            }, array_keys($this->request->post));

            $data = array_combine($keys, array_values($this->request->post));

            $this->model_setting_setting->editSetting('payment_pagseguro', $data);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->buildUrl('marketplace/extension', [
                'type' => 'payment'
            ]));
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

        /** Erros */
        foreach($this->error as $key => $value) {
            $data['error_field_' . $key] = $value;
        }

        /** Captura configurações salvas */
        foreach($this->getAllFields() as $key => $value) {
            $data[$key] = $this->request->post[$key] ?? $this->config->get(self::FIELD_PREFIX . $key);
        }

        if (empty($data['callback_token'])) {
            $data['callback_token'] = token(32);
        }

        $this->load->model('customer/custom_field');

        $data['custom_fields'] = $this->model_customer_custom_field->getCustomFields();

        $this->load->model('localisation/order_status');

        $data['statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        $this->load->model('localisation/geo_zone');

        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        $this->load->model('setting/store');

        $data['stores'] = $this->model_setting_store->getStores();

        $data['stores'][] = [
            'store_id' => 0,
            'name'     => $this->config->get('config_name') . $this->language->get('text_default')
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
     * Realiza a validação dos campos
     *
     * @return bool
     */
    private function validate()
    {
        $required_fields = array_filter($this->getAllFields(), function($item) {
            return $item['required'] === true;
        });

        $required_fields = array_keys($required_fields);

        if (!$this->user->hasPermission('modify', 'extension/payment/pagseguro')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        foreach($required_fields as $field) {
            if (empty($this->request->post[$field])) {
                $this->error[$field] = $this->language->get('error_required');
            }
        }

        if (!filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
        $this->error['email'] = $this->language->get('error_email');
        }

        if (!filter_var($this->request->post['installment_total'], FILTER_VALIDATE_INT, [
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

    /**
     * Retorna o nome de dos os campos do formulário
     *
     * @return array
     */
    private function getAllFields()
    {
        return [
            'status'                        => ['required' => false], // Obrigatório, porém sem necessidade de validação
            'email'                         => ['required' => true],
            'token'                         => ['required' => true],
            'sandbox'                       => ['required' => false], // Obrigatório, porém sem necessidade de validação
            'customer_notify'               => ['required' => false], // Obrigatório, porém sem necessidade de validação
            'callback_token'                => ['required' => true],
            'telemetry'                     => ['required' => false],
            'custom_fields_cpf'             => ['required' => false],
            'custom_fields_number'          => ['required' => false],
            'custom_fields_birthday'        => ['required' => false],
            'discount_boleto'               => ['required' => false],
            'discount_credit'               => ['required' => false],
            'discount_debit'                => ['required' => false],
            'fee_boleto'                    => ['required' => false],
            'discount_debit'                => ['required' => false],
            'fee_boleto'                    => ['required' => false],
            'fee_credit'                    => ['required' => false],
            'fee_debit'                     => ['required' => false],
            'order_status_pending'          => ['required' => true],
            'order_status_analysing'        => ['required' => true],
            'order_status_paid'             => ['required' => true],
            'order_status_available'        => ['required' => true],
            'order_status_disputed'         => ['required' => true],
            'order_status_returned'         => ['required' => true],
            'order_status_cancelled'        => ['required' => true],
            'geo_zone_id'                   => ['required' => false],
            'geo_sort_order'                => ['required' => false],
            'geo_stores'                    => ['required' => false],
            'installment_total'             => ['required' => true],
            'installment_free'              => ['required' => false],
            'installment_minimum_value'     => ['required' => true],
            'methods_boleto_status'         => ['required' => false],
            'methods_boleto_minimum_amount' => ['required' => false],
            'methods_credit_status'         => ['required' => false],
            'methods_credit_minimum_amount' => ['required' => false],
            'methods_debit_status'          => ['required' => false],
            'methods_debit_minimum_amount'  => ['required' => false],
            'layout'                        => ['required' => true],
        ];
    }
}
