<?php

require_once DIR_SYSTEM . 'library/PagSeguro/autoload.php';

use ValdeirPsr\PagSeguro\Domains\Logger\Logger;
use ValdeirPsr\PagSeguro\Request\Sale;
use ValdeirPsr\PagSeguro\Domains\Environment;

class ControllerExtensionPaymentPagseguro extends Controller
{
    const EXTENSION_PREFIX = 'payment_pagseguro_';
    const EXTENSION_VERSION = '2.1.0';
    const PAGSEGURO_LOG = DIR_SYSTEM . 'library/PagSeguro/valdeirpsr/pagseguro/log';

    private $error = [];

    /**
     * Exibe o formulário de configuração do módulo
     */
    public function index()
    {
        if (!defined('PAGSEGURO_LOG')) {
            define('PAGSEGURO_LOG', DIR_SYSTEM . 'library/PagSeguro/valdeirpsr/pagseguro/log');
        }

        $data = $this->load->language('extension/payment/pagseguro');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->document->addScript('https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js');
        $this->document->addScript('https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js');
        $this->document->addScript('https://cdn.jsdelivr.net/gh/opencart-extension/PagSeguro-Checkout-Transparente@config/lib/bundle.js');
        $this->document->addScript('https://cdn.jsdelivr.net/npm/sweetalert2@10');
        $this->document->addStyle('https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker-standalone.min.css');

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {
            $this->load->model('setting/setting');

            $keys = array_map(function($key) {
                return self::EXTENSION_PREFIX . $key;
            }, array_keys($this->request->post));

            $data = array_combine($keys, array_values($this->request->post));

            /** Salva os dados de configuração */
            $this->model_setting_setting->editSetting('payment_pagseguro', $data);

            /** Ativa/desativa o pagamento via Boleto */
            $this->model_setting_setting->editSetting('payment_pagseguro_boleto', [
                'payment_pagseguro_boleto_status' => $this->request->post['methods_boleto_status']
            ]);

            /** Ativa/desativa o pagamento via Cartão de Crédito */
            $this->model_setting_setting->editSetting('payment_pagseguro_credit', [
                'payment_pagseguro_credit_status' => $this->request->post['methods_credit_status']
            ]);

            /** Ativa/desativa o pagamento via Débito */
            $this->model_setting_setting->editSetting('payment_pagseguro_debit', [
                'payment_pagseguro_debit_status' => $this->request->post['methods_debit_status']
            ]);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->telemetry();
            $this->newsletter();

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
            $data[$key] = $this->request->post[$key] ?? $this->config->get(self::EXTENSION_PREFIX . $key);
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

        $data['themes_boleto'] = $this->getThemes('boleto');
        $data['themes_credit'] = $this->getThemes('credit');
        $data['themes_debit'] = $this->getThemes('debit');

        $data['logs_date'] = [];

        if (!is_dir(PAGSEGURO_LOG)) {
            mkdir(PAGSEGURO_LOG, 0777, true);
        }

        if (is_dir(PAGSEGURO_LOG) && is_readable(PAGSEGURO_LOG)) {
            $logs = new DirectoryIterator(PAGSEGURO_LOG);

            foreach ($logs as $log) {
                if ($log->isFile()) {
                    $data['logs_date'][] = preg_replace('/^(\d{4}-\d{2}-\d{2}).+/', '$1', $log->getFilename());
                }
            }
        }

        if (!is_dir(PAGSEGURO_LOG) || !is_writable(PAGSEGURO_LOG)) {
            $data['warning'] = sprintf($this->language->get('error_permission_dir'), PAGSEGURO_LOG);
        }

        if ($this->currency->getValue('BRL') <= 0) {
            $data['error_currency_code'] = $this->language->get('error_currency_code');
        } else {
            $data['error_currency_code'] = false;
        }

        if (version_compare(PHP_VERSION, '7.3', '>=') < 0) {
            $data['error_php_version'] = sprintf($this->language->get('error_php_version'), PHP_VERSION);
        } else {
            $data['error_php_version'] = false;
        }

        if (extension_loaded('curl') === false) {
            $data['error_php_curl'] = $this->language->get('error_php_curl');
        } else {
            $data['error_php_curl'] = false;
        }

        if (extension_loaded('json') === false) {
            $data['error_php_json'] = $this->language->get('error_php_json');
        } else {
            $data['error_php_json'] = false;
        }

        $data['action'] = $this->buildUrl('extension/payment/pagseguro');
        $data['create_custom_field_link'] = $this->buildUrl('customer/custom_field');
        $data['cancel'] = $this->buildUrl('marketplace/extension', [
            'type' => 'payment'
        ]);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/payment/pagseguro', $data));
    }

    /**
     * Gerencia logs
     */
    public function log()
    {
        $date = $this->request->get['date'] ?? null;
        $request_method = $this->request->server['REQUEST_METHOD'] ?? 'GET';
        $file = self::PAGSEGURO_LOG . "/{$date}.log";
        $isValid = $date && file_exists($file);

        if ($request_method == 'GET' && $isValid) {
            header('Content-Description: File Transfer');
            header('Content-Type: text/html');
            header('Content-Disposition: attachment');
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            echo file_get_contents($file);
            exit;

        } elseif ($request_method == 'DELETE' && $isValid) {
            unlink($file);
        }
    }

    /**
     * Cancela uma transação no PagSeguro
     */
    public function cancel_order()
    {
        Logger::getInstance([
            'enabled' => $this->config->get(self::EXTENSION_PREFIX . 'debug')
        ]);

        $this->load->language('extension/payment/pagseguro');

        $order_id = $this->request->get['order_id'] ?? 0;

        $this->load->model('extension/payment/pagseguro');
        $this->load->model('sale/order');

        $transaction_info = $this->model_extension_payment_pagseguro->getTransactionInfo(
            $order_id,
            ['code']
        );

        if (!isset($transaction_info['code'])) {
            header('HTTP/1.0 404 Not Found');
            return;
        }

        try {
            $request = new Sale($this->buildEnv());
            $result = $request->void($transaction_info['code']);

            if ($result) {
                $this->session->data['pagseguro_success'] = $this->language->get('text_void_success');
                $order_status_id = $this->config->get(self::EXTENSION_PREFIX . 'order_status_cancelled');
                $this->db->query('UPDATE ' . DB_PREFIX . 'order SET order_status_id = "' . $order_status_id . '" WHERE order_id = "' . intval($order_id) . '"');
                $this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . intval($order_id) . "', order_status_id = '" . $order_status_id . "', notify = '0', comment = '', date_added = NOW()");
            } else {
                $this->session->data['pagseguro_failed'] = $this->language->get('text_void_failed');
            }
        } catch (Exception $e) {
            $this->session->data['pagseguro_failed'] = $this->language->get('text_void_failed');
        }

        $this->response->redirect(
            $this->url->link(
                'sale/order/info',
                'order_id=' . $order_id .
                '&user_token=' . $this->session->data['user_token']
            )
        );
    }

    /**
     * Realiza reembolso total no PagSeguro
     */
    public function refund_order()
    {
        Logger::getInstance([
            'enabled' => $this->config->get(self::EXTENSION_PREFIX . 'debug')
        ]);

        $this->load->language('extension/payment/pagseguro');

        $order_id = $this->request->get['order_id'] ?? 0;

        $this->load->model('extension/payment/pagseguro');
        $this->load->model('sale/order');

        $transaction_info = $this->model_extension_payment_pagseguro->getTransactionInfo(
            $order_id,
            ['code']
        );

        if (!isset($transaction_info['code'])) {
            header('HTTP/1.0 404 Not Found');
            return;
        }

        try {
            $request = new Sale($this->buildEnv());
            $result = $request->refund($transaction_info['code']);

            if ($result) {
                $this->session->data['pagseguro_success'] = $this->language->get('text_refund_success');
                $order_status_id = $this->config->get(self::EXTENSION_PREFIX . 'order_status_returned');
                $this->db->query('UPDATE ' . DB_PREFIX . 'order SET order_status_id = "' . $order_status_id . '" WHERE order_id = "' . intval($order_id) . '"');
                $this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . intval($order_id) . "', order_status_id = '" . $order_status_id . "', notify = '0', comment = '', date_added = NOW()");
            } else {
                $this->session->data['pagseguro_failed'] = $this->language->get('text_refund_failed');
            }
        } catch (Exception $e) {
            $this->session->data['pagseguro_failed'] = $this->language->get('text_refund_failed');
        }

        $this->response->redirect(
            $this->url->link(
                'sale/order/info',
                'order_id=' . $order_id .
                '&user_token=' . $this->session->data['user_token']
            )
        );
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
                'max_range' => 18
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
            'debug'                         => ['required' => false],
            'customer_notify'               => ['required' => false], // Obrigatório, porém sem necessidade de validação
            'callback_token'                => ['required' => true],
            'telemetry'                     => ['required' => false],
            'newsletter'                    => ['required' => false],
            'custom_fields_cpf'             => ['required' => true],
            'custom_fields_number'          => ['required' => true],
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
            'methods_boleto_title'          => ['required' => true],
            'methods_boleto_status'         => ['required' => false],
            'methods_boleto_minimum_amount' => ['required' => false],
            'methods_credit_title'          => ['required' => true],
            'methods_credit_status'         => ['required' => false],
            'methods_credit_minimum_amount' => ['required' => false],
            'methods_debit_title'           => ['required' => true],
            'methods_debit_status'          => ['required' => false],
            'methods_debit_minimum_amount'  => ['required' => false],
            'theme_boleto'                  => ['required' => true],
            'theme_credit'                  => ['required' => true],
            'theme_debit'                   => ['required' => true]
        ];
    }

    /**
     * Envia os dados para telemetria
     */
    private function telemetry()
    {
        $url = $this->request->post['telemetry_url'] ?? false;

        if ($this->request->post['telemetry'] && $url) {
            $fields_remove = [
                'email',
                'token',
                'callback_token',
                'newsletter'
            ];

            $data = array_filter($this->request->post, function($value, $key) use ($fields_remove) {
                return !in_array($key, $fields_remove);
            }, ARRAY_FILTER_USE_BOTH);

            $fields = [
                'version' => self::EXTENSION_VERSION,
                'uuid' => sha1($this->request->post['email']),
                'plataform' => 'OpenCart ' . VERSION,
                'module' => 'pagseguro_checkout_transparente',
                'data' => $data
            ];

            ob_start();
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_USERAGENT, 'PagSeguro Checkout Transparente for OpenCart');
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($fields));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, false);
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json'
            ]);
            curl_exec($curl);
            curl_close($curl);
            ob_end_clean();
        }
    }

    /**
     * Cadastra e-mail para receber alertas
     */
    private function newsletter()
    {
        $url = $this->request->post['newsletter_url'] ?? false;

        if (!$url) return;

        $method = !empty($this->request->post['newsletter'])
            ? 'POST'
            : 'DELETE';

        $fields = [
            'email' => $this->request->post['newsletter'],
            'plataform' => 'OpenCart ' . VERSION,
            'module' => 'pagseguro_checkout_transparente'
        ];

        $fields['ref'] = sha1(json_encode($fields));

        if ($method === 'DELETE') {
            $url .= '/' . $fields['ref'];
        }

        ob_start();
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, 'PagSeguro Checkout Transparente for OpenCart');
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($fields));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        curl_exec($curl);
        curl_close($curl);
        ob_end_clean();
    }

    /**
     * Instala e adiciona permissão para editar os módulos de pagamento:
     *  - PagSeguro Boleto
     *  - PagSeguro Cartão de Crédito
     *  - PagSeguro Débito
     */
    public function install()
    {
        $this->load->model('setting/extension');

        $this->model_setting_extension->install('payment', 'pagseguro_boleto');
        $this->model_setting_extension->install('payment', 'pagseguro_credit');
        $this->model_setting_extension->install('payment', 'pagseguro_debit');
        $this->model_setting_extension->install('total', 'pagseguro_discount');
        $this->model_setting_extension->install('total', 'pagseguro_fee');

        $this->load->model('user/user_group');

        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/payment/pagseguro_boleto');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/payment/pagseguro_boleto');

        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/payment/pagseguro_credit');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/payment/pagseguro_credit');

        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/payment/pagseguro_debit');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/payment/pagseguro_debit');

        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/total/pagseguro_discount');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/total/pagseguro_discount');

        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/total/pagseguro_fee');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/total/pagseguro_fee');

        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'sale/pagseguro_manager_order/cancel');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'sale/pagseguro_manager_order/refund');

        $this->load->model('setting/setting');

        $this->model_setting_setting->editSetting('total_pagseguro_discount', [
            'total_pagseguro_discount_status' => true
        ]);

        $this->model_setting_setting->editSetting('total_pagseguro_fee', [
            'total_pagseguro_fee_status' => true
        ]);

        $this->load->model('extension/payment/pagseguro');

        $this->model_extension_payment_pagseguro->createTables();

        $this->load->model('setting/event');

        $this->model_setting_event->addEvent('pagseguro', 'catalog/view/account/order_info/before', 'extension/payment/pagseguro/boleto2');

        if (!is_dir(self::PAGSEGURO_LOG)) {
            mkdir(self::PAGSEGURO_LOG, 0777, true);
        }
    }

    /**
     * Remove permissões para editar os módulos de pagamento:
     *  - PagSeguro Boleto
     *  - PagSeguro Cartão de Crédito
     *  - PagSeguro Débito
     *
     * Remove as tabelas criadas pelo módulo
     */
    public function uninstall()
    {
        $this->load->model('setting/extension');

        $this->model_setting_extension->uninstall('payment', 'pagseguro_boleto');
        $this->model_setting_extension->uninstall('payment', 'pagseguro_credit');
        $this->model_setting_extension->uninstall('payment', 'pagseguro_debit');
        $this->model_setting_extension->uninstall('total', 'pagseguro_discount');
        $this->model_setting_extension->uninstall('total', 'pagseguro_fee');

        $this->load->model('user/user_group');

        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'access', 'extension/payment/pagseguro_boleto');
        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', 'extension/payment/pagseguro_boleto');

        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'access', 'extension/payment/pagseguro_credit');
        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', 'extension/payment/pagseguro_credit');

        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'access', 'extension/payment/pagseguro_debit');
        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', 'extension/payment/pagseguro_debit');

        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'access', 'extension/total/pagseguro_discount');
        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', 'extension/total/pagseguro_discount');

        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'access', 'extension/total/pagseguro_fee');
        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', 'extension/total/pagseguro_fee');

        $this->load->model('setting/setting');

        $this->model_setting_setting->editSetting('total_pagseguro_discount', [
            'total_pagseguro_discount_status' => false
        ]);

        $this->model_setting_setting->editSetting('total_pagseguro_fee', [
            'total_pagseguro_fee_status' => false
        ]);

        $this->load->model('extension/payment/pagseguro');

        $this->model_extension_payment_pagseguro->dropTables();

        $this->load->model('setting/event');

        $this->model_setting_event->deleteEventByCode('pagseguro');

        $config = file_get_contents('https://cdn.jsdelivr.net/gh/opencart-extension/PagSeguro-Checkout-Transparente@config/config/config.json');

        $config = @json_decode($config);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return;
        }

        if (isset($config->telemetry)) {
            $this->request->post['telemetry_url'] = $config->telemetry->url ?? false;
            $this->request->post['telemetry'] = 1;
            $this->request->post['email'] = 'none';
            $this->request->post['action'] = 'remove';
            $this->telemetry();
        }
    }

    /**
     * Captura os temas das formas de pagamento
     *
     * @param string $paymentType boleto, credit ou debit
     *
     * @return []
     */
    private function getThemes(string $paymentType)
    {
        $path = DIR_CATALOG . 'view/theme/*/template/extension/payment/pagseguro_' . $paymentType . '*';

        $obj = new GlobIterator($path, FilesystemIterator::KEY_AS_FILENAME);

        $themes = [];

        foreach ($obj as $o) {
            $parser = Spatie\YamlFrontMatter\YamlFrontMatter::parseFile($o->getPathname());

            $themes[] = array_merge(
                $parser->matter(),
                [
                    'description' => preg_replace('/\n\r?/', '', nl2br($parser->matter('description'))),
                    'filename' => pathinfo($o->getFilename(), PATHINFO_FILENAME)
                ]
            );
        }

        return $themes;
    }

    /**
     * Cria um ambiente de desenvolvimento
     *
     * @return Environment
     */
    private function buildEnv()
    {
        $email = $this->config->get(self::EXTENSION_PREFIX . 'email');
        $token = $this->config->get(self::EXTENSION_PREFIX . 'token');
        $sandbox = $this->config->get(self::EXTENSION_PREFIX . 'sandbox');

        if ($sandbox) {
            return Environment::sandbox($email, $token);
        } else {
            return Environment::production($email, $token);
        }
    }
}
