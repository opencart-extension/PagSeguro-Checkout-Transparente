<?php
class ControllerExtensionPaymentPagseguro extends Controller {
	
	private $error = array();
	
	public function index() {
		
		/* Carrega linguagem */
		$data = $this->load->language('extension/payment/pagseguro');
		
		$this->document->setTitle($this->language->get('heading_title'));
        
        $user_token = $this->session->data['user_token'];
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->load->model('setting/setting');
			
			$this->model_setting_setting->editSetting('payment_pagseguro', $this->request->post);
                        
			$this->model_setting_setting->editSetting('total_pagseguro', [
                'total_pagseguro_acrescimo_status' => $this->request->post['payment_pagseguro_status'],
                'total_pagseguro_desconto_status'  => $this->request->post['payment_pagseguro_status'],
            ]);
            
			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $user_token, true));
		}
		
		/* Load Models */
		$this->load->model('localisation/order_status');
		$this->load->model('localisation/geo_zone');
		$this->load->model('customer/custom_field');
		
		
		/* Warning */
		if (isset($this->error['warning'])) {
			$data['warning'] = $this->error['warning'];
		} else {
			$data['warning'] = false;
		}
		
		/* Error Email */
		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = false;
		}
		
		/* Error Token */
		if (isset($this->error['token'])) {
			$data['error_token'] = $this->error['token'];
		} else {
			$data['error_token'] = false;
		}
		
		/* Error Quantidade de Parcelas */
		if (isset($this->error['qnt_parcelas'])) {
			$data['error_qnt_parcela'] = $this->error['qnt_parcelas'];
		} else {
			$data['error_qnt_parcela'] = false;
		}
		
		/* Error Parcelas Sem Juros */
		if (isset($this->error['parcelas_sem_juros'])) {
			$data['error_parcelas_sem_juros'] = $this->error['parcelas_sem_juros'];
		} else {
			$data['error_parcelas_sem_juros'] = false;
		}
		
		$data['breadcrumbs'] = array();
		
		$data['breadcrumbs'][] = array(
			'href' => $this->url->link('common/home', 'user_token=' . $user_token, true),
			'name' => $this->language->get('text_home')
		);
		
		$data['breadcrumbs'][] = array(
			'href' => $this->url->link('extension/extension', 'user_token=' . $user_token, true),
			'name' => $this->language->get('text_payment')
		);
		
		$data['breadcrumbs'][] = array(
			'href' => $this->url->link('extension/payment/pagseguro', 'user_token=' . $user_token, true),
			'name' => $this->language->get('heading_title')
		);
		
		/* Status */
		if (isset($this->request->post['payment_pagseguro_status'])) {
			$data['payment_pagseguro_status'] = $this->request->post['payment_pagseguro_status'];
		} else {
			$data['payment_pagseguro_status'] = $this->config->get('payment_pagseguro_status');
		}
		
		/* Email */
		if (isset($this->request->post['payment_pagseguro_email'])) {
			$data['payment_pagseguro_email'] = $this->request->post['payment_pagseguro_email'];
		} else {
			$data['payment_pagseguro_email'] = $this->config->get('payment_pagseguro_email');
		}
		
		/* Token */
		if (isset($this->request->post['payment_pagseguro_token'])) {
			$data['payment_pagseguro_token'] = $this->request->post['payment_pagseguro_token'];
		} else {
			$data['payment_pagseguro_token'] = $this->config->get('payment_pagseguro_token');
		}
		
		/* Modo Teste */
		if (isset($this->request->post['payment_pagseguro_modo_teste'])) {
			$data['payment_pagseguro_modo_teste'] = $this->request->post['payment_pagseguro_modo_teste'];
		} else {
			$data['payment_pagseguro_modo_teste'] = $this->config->get('payment_pagseguro_modo_teste');
		}
		
		/* Debug */
		if (isset($this->request->post['payment_pagseguro_debug'])) {
			$data['payment_pagseguro_debug'] = $this->request->post['payment_pagseguro_debug'];
		} else {
			$data['payment_pagseguro_debug'] = $this->config->get('payment_pagseguro_debug');
		}
		
		/* Notificar Cliente */
		if (isset($this->request->post['payment_pagseguro_notificar_cliente'])) {
			$data['payment_pagseguro_notificar_cliente'] = $this->request->post['payment_pagseguro_notificar_cliente'];
		} else {
			$data['payment_pagseguro_notificar_cliente'] = $this->config->get('payment_pagseguro_notificar_cliente');
		}
		
		/* Custom Field Número */
		if (isset($this->request->post['payment_pagseguro_numero_residencia'])) {
			$data['payment_pagseguro_numero_residencia'] = $this->request->post['payment_pagseguro_numero_residencia'];
		} else {
			$data['payment_pagseguro_numero_residencia'] = $this->config->get('payment_pagseguro_numero_residencia');
		}
		
		/* Custom Field Data de Nascimento */
		if (isset($this->request->post['payment_pagseguro_data_nascimento'])) {
			$data['payment_pagseguro_data_nascimento'] = $this->request->post['payment_pagseguro_data_nascimento'];
		} else {
			$data['payment_pagseguro_data_nascimento'] = $this->config->get('payment_pagseguro_data_nascimento');
		}
		
		/* Custom Field CPF */
		if (isset($this->request->post['payment_pagseguro_cpf'])) {
			$data['payment_pagseguro_cpf'] = $this->request->post['payment_pagseguro_cpf'];
		} else {
			$data['payment_pagseguro_cpf'] = $this->config->get('payment_pagseguro_cpf');
		}
		
		/* Desconto Boleto */
		if (isset($this->request->post['payment_pagseguro_desconto_boleto'])) {
			$data['payment_pagseguro_desconto_boleto'] = $this->request->post['payment_pagseguro_desconto_boleto'];
		} else {
			$data['payment_pagseguro_desconto_boleto'] = $this->config->get('payment_pagseguro_desconto_boleto');
		}
		
		/* Desconto Cartão de Crédito */
		if (isset($this->request->post['payment_pagseguro_desconto_cartao'])) {
			$data['payment_pagseguro_desconto_cartao'] = $this->request->post['payment_pagseguro_desconto_cartao'];
		} else {
			$data['payment_pagseguro_desconto_cartao'] = $this->config->get('payment_pagseguro_desconto_cartao');
		}
		
		/* Desconto Débito */
		if (isset($this->request->post['payment_pagseguro_desconto_debito'])) {
			$data['payment_pagseguro_desconto_debito'] = $this->request->post['payment_pagseguro_desconto_debito'];
		} else {
			$data['payment_pagseguro_desconto_debito'] = $this->config->get('payment_pagseguro_desconto_debito');
		}
		
		/* Acréscimo Boleto */
		if (isset($this->request->post['payment_pagseguro_acrescimo_boleto'])) {
			$data['payment_pagseguro_acrescimo_boleto'] = $this->request->post['payment_pagseguro_acrescimo_boleto'];
		} else {
			$data['payment_pagseguro_acrescimo_boleto'] = $this->config->get('payment_pagseguro_acrescimo_boleto');
		}
		
		/* Acréscimo Cartão de Crédito */
		if (isset($this->request->post['payment_pagseguro_acrescimo_cartao'])) {
			$data['payment_pagseguro_acrescimo_cartao'] = $this->request->post['payment_pagseguro_acrescimo_cartao'];
		} else {
			$data['payment_pagseguro_acrescimo_cartao'] = $this->config->get('payment_pagseguro_acrescimo_cartao');
		}
		
		/* Acréscimo Débito */
		if (isset($this->request->post['payment_pagseguro_acrescimo_debito'])) {
			$data['payment_pagseguro_acrescimo_debito'] = $this->request->post['payment_pagseguro_acrescimo_debito'];
		} else {
			$data['payment_pagseguro_acrescimo_debito'] = $this->config->get('payment_pagseguro_acrescimo_debito');
		}
		
		/* Aguardando Pagamento */
		if (isset($this->request->post['payment_pagseguro_aguardando_pagamento'])) {
			$data['payment_pagseguro_aguardando_pagamento'] = $this->request->post['payment_pagseguro_aguardando_pagamento'];
		} else {
			$data['payment_pagseguro_aguardando_pagamento'] = $this->config->get('payment_pagseguro_aguardando_pagamento');
		}
		
		/* Em Anaálise */
		if (isset($this->request->post['payment_pagseguro_analise'])) {
			$data['payment_pagseguro_analise'] = $this->request->post['payment_pagseguro_analise'];
		} else {
			$data['payment_pagseguro_analise'] = $this->config->get('payment_pagseguro_analise');
		}
		
		/* Paga (Pago|Completo) */
		if (isset($this->request->post['payment_pagseguro_paga'])) {
			$data['payment_pagseguro_paga'] = $this->request->post['payment_pagseguro_paga'];
		} else {
			$data['payment_pagseguro_paga'] = $this->config->get('payment_pagseguro_paga');
		}
		
		/* Disponível */
		if (isset($this->request->post['payment_pagseguro_disponivel'])) {
			$data['payment_pagseguro_disponivel'] = $this->request->post['payment_pagseguro_disponivel'];
		} else {
			$data['payment_pagseguro_disponivel'] = $this->config->get('payment_pagseguro_disponivel');
		}
		
		/* Disputa */
		if (isset($this->request->post['payment_pagseguro_disputa'])) {
			$data['payment_pagseguro_disputa'] = $this->request->post['payment_pagseguro_disputa'];
		} else {
			$data['payment_pagseguro_disputa'] = $this->config->get('payment_pagseguro_disputa');
		}
		
		/* Devolvido (Reembolsado) */
		if (isset($this->request->post['payment_pagseguro_devolvida'])) {
			$data['payment_pagseguro_devolvida'] = $this->request->post['payment_pagseguro_devolvida'];
		} else {
			$data['payment_pagseguro_devolvida'] = $this->config->get('payment_pagseguro_devolvida');
		}
		
		/* Cancelado */
		if (isset($this->request->post['payment_pagseguro_cancelada'])) {
			$data['payment_pagseguro_cancelada'] = $this->request->post['payment_pagseguro_cancelada'];
		} else {
			$data['payment_pagseguro_cancelada'] = $this->config->get('payment_pagseguro_cancelada');
		}
		
		/* Zona Geográfica */
		if (isset($this->request->post['payment_pagseguro_geo_zone'])) {
			$data['payment_pagseguro_geo_zone'] = $this->request->post['payment_pagseguro_geo_zone'];
		} else {
			$data['payment_pagseguro_geo_zone'] = $this->config->get('payment_pagseguro_geo_zone');
		}
		
		/* Ordem */
		if (isset($this->request->post['payment_pagseguro_sort_order'])) {
			$data['payment_pagseguro_sort_order'] = $this->request->post['payment_pagseguro_sort_order'];
		} else {
			$data['payment_pagseguro_sort_order'] = $this->config->get('payment_pagseguro_sort_order');
		}
		
		/* Quantidade de parcelas */
		if (isset($this->request->post['payment_pagseguro_qnt_parcelas'])) {
			$data['payment_pagseguro_qnt_parcelas'] = $this->request->post['payment_pagseguro_qnt_parcelas'];
		} else {
			$data['payment_pagseguro_qnt_parcelas'] = $this->config->get('payment_pagseguro_qnt_parcelas');
		}
		
		/* Parcelas sem juros */
		if (isset($this->request->post['payment_pagseguro_parcelas_sem_juros'])) {
			$data['payment_pagseguro_parcelas_sem_juros'] = $this->request->post['payment_pagseguro_parcelas_sem_juros'];
		} else {
			$data['payment_pagseguro_parcelas_sem_juros'] = $this->config->get('payment_pagseguro_parcelas_sem_juros');
		}
		
		/* Boleto */
		if (isset($this->request->post['payment_pagseguro_boleto_status'])) {
			$data['payment_pagseguro_boleto_status'] = $this->request->post['payment_pagseguro_boleto_status'];
		} else {
			$data['payment_pagseguro_boleto_status'] = $this->config->get('payment_pagseguro_boleto_status');
		}
		
		/* Valor minimo boleto */
		if (isset($this->request->post['payment_pagseguro_valor_minimo_boleto'])) {
			$data['payment_pagseguro_valor_minimo_boleto'] = $this->request->post['payment_pagseguro_valor_minimo_boleto'];
		} else {
			$data['payment_pagseguro_valor_minimo_boleto'] = $this->config->get('payment_pagseguro_valor_minimo_boleto');
		}
		
		/* Cartão de Crédito */
		if (isset($this->request->post['payment_pagseguro_cartao_status'])) {
			$data['payment_pagseguro_cartao_status'] = $this->request->post['payment_pagseguro_cartao_status'];
		} else {
			$data['payment_pagseguro_cartao_status'] = $this->config->get('payment_pagseguro_cartao_status');
		}
		
		/* Valor minimo cartão */
		if (isset($this->request->post['payment_pagseguro_valor_minimo_cartao'])) {
			$data['payment_pagseguro_valor_minimo_cartao'] = $this->request->post['payment_pagseguro_valor_minimo_cartao'];
		} else {
			$data['payment_pagseguro_valor_minimo_cartao'] = $this->config->get('payment_pagseguro_valor_minimo_cartao');
		}
		
		/* Débito */
		if (isset($this->request->post['payment_pagseguro_debito_status'])) {
			$data['payment_pagseguro_debito_status'] = $this->request->post['payment_pagseguro_debito_status'];
		} else {
			$data['payment_pagseguro_debito_status'] = $this->config->get('payment_pagseguro_debito_status');
		}
		
		/* Valor minimo debito */
		if (isset($this->request->post['payment_pagseguro_valor_minimo_debito'])) {
			$data['payment_pagseguro_valor_minimo_debito'] = $this->request->post['payment_pagseguro_valor_minimo_debito'];
		} else {
			$data['payment_pagseguro_valor_minimo_debito'] = $this->config->get('payment_pagseguro_valor_minimo_debito');
		}
		
		/* Status de Pagamento */
		$data['statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		/* Zonas Geográficas */
		$data['zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		/* Custom Field */
		$data['custom_fields'] = $this->model_customer_custom_field->getCustomFields();
		
		/* Debug */
		if (file_exists(DIR_LOGS . 'pagseguro.log')) {
			if ((isset($this->request->post['payment_pagseguro_debug']) && $this->request->post['payment_pagseguro_debug'])) {
				$data['debug'] = file(DIR_LOGS . 'pagseguro.log');
			} elseif ($this->config->get('payment_pagseguro_debug')) {
				$data['debug'] = file(DIR_LOGS . 'pagseguro.log');
			} else {
				$data['debug'] = array();
			}
		} else {
			$data['debug'] = array();
		}
		
		/* Links */
		$data['action'] = $this->url->link('extension/payment/pagseguro', 'user_token=' . $user_token, true);
		$data['cancel'] = $this->url->link('extension/extension', 'user_token=' . $user_token, true);
		
        $data['link_custom_field'] = $this->url->link('customer/custom_field', 'user_token=' . $user_token, true);
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$this->response->setOutput($this->load->view('extension/payment/pagseguro', $data));
	}
	
	public function validate() {
        
        /* Error Permission */
		if (!$this->user->hasPermission('modify', 'extension/payment/pagseguro')) {
			$this->error['warning'] = $this->language->get('warning');
		}
		
		/* Error Email */
		if (!filter_var($this->request->post['payment_pagseguro_email'], FILTER_VALIDATE_EMAIL)) {
			$this->error['email'] = $this->language->get('error_email');
		}
		
		/* Error Token */
		if (strlen($this->request->post['payment_pagseguro_token']) < 32) {
			$this->error['token'] = $this->language->get('error_token');
		}
		
		/* Error Quantidade de Parcelas */
		if (!filter_var($this->request->post['payment_pagseguro_qnt_parcelas'], FILTER_VALIDATE_INT)) {
			$this->error['qnt_parcelas'] = $this->language->get('error_qnt_parcela');
		} elseif ($this->request->post['payment_pagseguro_qnt_parcelas'] > 18) {
			$this->error['qnt_parcelas'] = $this->language->get('error_qnt_parcela_invalido');
		}
		
		/* Erorr Quantidade Parcelas sem Juros */
		if (!filter_var($this->request->post['payment_pagseguro_parcelas_sem_juros'], FILTER_VALIDATE_INT)) {
			$this->error['parcelas_sem_juros'] = $this->language->get('error_parcelas_sem_juros');
		} elseif ($this->request->post['payment_pagseguro_parcelas_sem_juros'] > 18) {
			$this->error['parcelas_sem_juros'] = $this->language->get('error_parcelas_sem_juros_invalido');
		}
		
		/* Error Boleto */
		if ($this->request->post['payment_pagseguro_boleto_status']) {
			if (!filter_var($this->request->post['payment_pagseguro_valor_minimo_boleto'], FILTER_VALIDATE_FLOAT)) {
				$this->request->post['payment_pagseguro_valor_minimo_boleto'] = 1.00;
			}
		}
		
		/* Error Cartão de Crédito */
		if ($this->request->post['payment_pagseguro_cartao_status']) {
			if (!filter_var($this->request->post['payment_pagseguro_valor_minimo_cartao'], FILTER_VALIDATE_FLOAT)) {
				$this->request->post['payment_pagseguro_valor_minimo_cartao'] = 1.00;
			}
		}
		
		/* Error Débito */
		if ($this->request->post['payment_pagseguro_debito_status']) {
			if (!filter_var($this->request->post['payment_pagseguro_valor_minimo_debito'], FILTER_VALIDATE_FLOAT)) {
				$this->request->post['payment_pagseguro_valor_minimo_debito'] = 1.00;
			}
		}
		
		return !$this->error;
	}
	
	public function install() {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "extension` (`type`, `code`) VALUES ('payment', 'pagseguro_boleto') ");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "extension` (`type`, `code`) VALUES ('payment', 'pagseguro_cartao') ");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "extension` (`type`, `code`) VALUES ('payment', 'pagseguro_debito') ");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "extension` (`type`, `code`) VALUES ('total', 'pagseguro_acrescimo') ");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "extension` (`type`, `code`) VALUES ('total', 'pagseguro_desconto') ");
	}
	
    public function uninstall() {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "extension` WHERE `code` = 'pagseguro_boleto';");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "extension` WHERE `code` = 'pagseguro_cartao';");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "extension` WHERE `code` = 'pagseguro_debito';");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "extension` WHERE `code` = 'pagseguro_acrescimo';");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "extension` WHERE `code` = 'pagseguro_desconto';");
	}
}