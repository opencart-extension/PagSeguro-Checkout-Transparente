<?php
class ControllerExtensionPaymentPagseguro extends Controller {
	
	private $error = array();
	
	public function index() {
		
		/* Carrega linguagem */
		$data = $this->load->language('extension/payment/pagseguro');
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->load->model('setting/setting');
			
			$this->model_setting_setting->editSetting('pagseguro', $this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'], true));
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
			'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], true),
			'name' => $this->language->get('text_home')
		);
		
		$data['breadcrumbs'][] = array(
			'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'], true),
			'name' => $this->language->get('text_payment')
		);
		
		$data['breadcrumbs'][] = array(
			'href' => $this->url->link('extension/payment/pagseguro', 'token=' . $this->session->data['token'], true),
			'name' => $this->language->get('heading_title')
		);
		
		/* Status */
		if (isset($this->request->post['pagseguro_status'])) {
			$data['pagseguro_status'] = $this->request->post['pagseguro_status'];
		} else {
			$data['pagseguro_status'] = $this->config->get('pagseguro_status');
		}
		
		/* Email */
		if (isset($this->request->post['pagseguro_email'])) {
			$data['pagseguro_email'] = $this->request->post['pagseguro_email'];
		} else {
			$data['pagseguro_email'] = $this->config->get('pagseguro_email');
		}
		
		/* Token */
		if (isset($this->request->post['pagseguro_token'])) {
			$data['pagseguro_token'] = $this->request->post['pagseguro_token'];
		} else {
			$data['pagseguro_token'] = $this->config->get('pagseguro_token');
		}
		
		/* Modo Teste */
		if (isset($this->request->post['pagseguro_modo_teste'])) {
			$data['pagseguro_modo_teste'] = $this->request->post['pagseguro_modo_teste'];
		} else {
			$data['pagseguro_modo_teste'] = $this->config->get('pagseguro_modo_teste');
		}
		
		/* Debug */
		if (isset($this->request->post['pagseguro_debug'])) {
			$data['pagseguro_debug'] = $this->request->post['pagseguro_debug'];
		} else {
			$data['pagseguro_debug'] = $this->config->get('pagseguro_debug');
		}
		
		/* Notificar Cliente */
		if (isset($this->request->post['pagseguro_notificar_cliente'])) {
			$data['pagseguro_notificar_cliente'] = $this->request->post['pagseguro_notificar_cliente'];
		} else {
			$data['pagseguro_notificar_cliente'] = $this->config->get('pagseguro_notificar_cliente');
		}
		
		/* Custom Field Número */
		if (isset($this->request->post['pagseguro_numero_residencia'])) {
			$data['pagseguro_numero_residencia'] = $this->request->post['pagseguro_numero_residencia'];
		} else {
			$data['pagseguro_numero_residencia'] = $this->config->get('pagseguro_numero_residencia');
		}
		
		/* Custom Field Data de Nascimento */
		if (isset($this->request->post['pagseguro_data_nascimento'])) {
			$data['pagseguro_data_nascimento'] = $this->request->post['pagseguro_data_nascimento'];
		} else {
			$data['pagseguro_data_nascimento'] = $this->config->get('pagseguro_data_nascimento');
		}
		
		/* Custom Field CPF */
		if (isset($this->request->post['pagseguro_cpf'])) {
			$data['pagseguro_cpf'] = $this->request->post['pagseguro_cpf'];
		} else {
			$data['pagseguro_cpf'] = $this->config->get('pagseguro_cpf');
		}
		
		/* Desconto Boleto */
		if (isset($this->request->post['pagseguro_desconto_boleto'])) {
			$data['pagseguro_desconto_boleto'] = $this->request->post['pagseguro_desconto_boleto'];
		} else {
			$data['pagseguro_desconto_boleto'] = $this->config->get('pagseguro_desconto_boleto');
		}
		
		/* Desconto Cartão de Crédito */
		if (isset($this->request->post['pagseguro_desconto_cartao'])) {
			$data['pagseguro_desconto_cartao'] = $this->request->post['pagseguro_desconto_cartao'];
		} else {
			$data['pagseguro_desconto_cartao'] = $this->config->get('pagseguro_desconto_cartao');
		}
		
		/* Desconto Débito */
		if (isset($this->request->post['pagseguro_desconto_debito'])) {
			$data['pagseguro_desconto_debito'] = $this->request->post['pagseguro_desconto_debito'];
		} else {
			$data['pagseguro_desconto_debito'] = $this->config->get('pagseguro_desconto_debito');
		}
		
		/* Acréscimo Boleto */
		if (isset($this->request->post['pagseguro_acrescimo_boleto'])) {
			$data['pagseguro_acrescimo_boleto'] = $this->request->post['pagseguro_acrescimo_boleto'];
		} else {
			$data['pagseguro_acrescimo_boleto'] = $this->config->get('pagseguro_acrescimo_boleto');
		}
		
		/* Acréscimo Cartão de Crédito */
		if (isset($this->request->post['pagseguro_acrescimo_cartao'])) {
			$data['pagseguro_acrescimo_cartao'] = $this->request->post['pagseguro_acrescimo_cartao'];
		} else {
			$data['pagseguro_acrescimo_cartao'] = $this->config->get('pagseguro_acrescimo_cartao');
		}
		
		/* Acréscimo Débito */
		if (isset($this->request->post['pagseguro_acrescimo_debito'])) {
			$data['pagseguro_acrescimo_debito'] = $this->request->post['pagseguro_acrescimo_debito'];
		} else {
			$data['pagseguro_acrescimo_debito'] = $this->config->get('pagseguro_acrescimo_debito');
		}
		
		/* Aguardando Pagamento */
		if (isset($this->request->post['pagseguro_aguardando_pagamento'])) {
			$data['pagseguro_aguardando_pagamento'] = $this->request->post['pagseguro_aguardando_pagamento'];
		} else {
			$data['pagseguro_aguardando_pagamento'] = $this->config->get('pagseguro_aguardando_pagamento');
		}
		
		/* Em Anaálise */
		if (isset($this->request->post['pagseguro_analise'])) {
			$data['pagseguro_analise'] = $this->request->post['pagseguro_analise'];
		} else {
			$data['pagseguro_analise'] = $this->config->get('pagseguro_analise');
		}
		
		/* Paga (Pago|Completo) */
		if (isset($this->request->post['pagseguro_paga'])) {
			$data['pagseguro_paga'] = $this->request->post['pagseguro_paga'];
		} else {
			$data['pagseguro_paga'] = $this->config->get('pagseguro_paga');
		}
		
		/* Disponível */
		if (isset($this->request->post['pagseguro_disponivel'])) {
			$data['pagseguro_disponivel'] = $this->request->post['pagseguro_disponivel'];
		} else {
			$data['pagseguro_disponivel'] = $this->config->get('pagseguro_disponivel');
		}
		
		/* Disputa */
		if (isset($this->request->post['pagseguro_disputa'])) {
			$data['pagseguro_disputa'] = $this->request->post['pagseguro_disputa'];
		} else {
			$data['pagseguro_disputa'] = $this->config->get('pagseguro_disputa');
		}
		
		/* Devolvido (Reembolsado) */
		if (isset($this->request->post['pagseguro_devolvida'])) {
			$data['pagseguro_devolvida'] = $this->request->post['pagseguro_devolvida'];
		} else {
			$data['pagseguro_devolvida'] = $this->config->get('pagseguro_devolvida');
		}
		
		/* Cancelado */
		if (isset($this->request->post['pagseguro_cancelada'])) {
			$data['pagseguro_cancelada'] = $this->request->post['pagseguro_cancelada'];
		} else {
			$data['pagseguro_cancelada'] = $this->config->get('pagseguro_cancelada');
		}
		
		/* Zona Geográfica */
		if (isset($this->request->post['pagseguro_geo_zone'])) {
			$data['pagseguro_geo_zone'] = $this->request->post['pagseguro_geo_zone'];
		} else {
			$data['pagseguro_geo_zone'] = $this->config->get('pagseguro_geo_zone');
		}
		
		/* Ordem */
		if (isset($this->request->post['pagseguro_sort_order'])) {
			$data['pagseguro_sort_order'] = $this->request->post['pagseguro_sort_order'];
		} else {
			$data['pagseguro_sort_order'] = $this->config->get('pagseguro_sort_order');
		}
		
		/* Quantidade de parcelas */
		if (isset($this->request->post['pagseguro_qnt_parcelas'])) {
			$data['pagseguro_qnt_parcelas'] = $this->request->post['pagseguro_qnt_parcelas'];
		} else {
			$data['pagseguro_qnt_parcelas'] = $this->config->get('pagseguro_qnt_parcelas');
		}
		
		/* Parcelas sem juros */
		if (isset($this->request->post['pagseguro_parcelas_sem_juros'])) {
			$data['pagseguro_parcelas_sem_juros'] = $this->request->post['pagseguro_parcelas_sem_juros'];
		} else {
			$data['pagseguro_parcelas_sem_juros'] = $this->config->get('pagseguro_parcelas_sem_juros');
		}
		
		/* Boleto */
		if (isset($this->request->post['pagseguro_boleto_status'])) {
			$data['pagseguro_boleto_status'] = $this->request->post['pagseguro_boleto_status'];
		} else {
			$data['pagseguro_boleto_status'] = $this->config->get('pagseguro_boleto_status');
		}
		
		/* Valor minimo boleto */
		if (isset($this->request->post['pagseguro_valor_minimo_boleto'])) {
			$data['pagseguro_valor_minimo_boleto'] = $this->request->post['pagseguro_valor_minimo_boleto'];
		} else {
			$data['pagseguro_valor_minimo_boleto'] = $this->config->get('pagseguro_valor_minimo_boleto');
		}
		
		/* Cartão de Crédito */
		if (isset($this->request->post['pagseguro_cartao_status'])) {
			$data['pagseguro_cartao_status'] = $this->request->post['pagseguro_cartao_status'];
		} else {
			$data['pagseguro_cartao_status'] = $this->config->get('pagseguro_cartao_status');
		}
		
		/* Valor minimo cartão */
		if (isset($this->request->post['pagseguro_valor_minimo_cartao'])) {
			$data['pagseguro_valor_minimo_cartao'] = $this->request->post['pagseguro_valor_minimo_cartao'];
		} else {
			$data['pagseguro_valor_minimo_cartao'] = $this->config->get('pagseguro_valor_minimo_cartao');
		}
		
		/* Débito */
		if (isset($this->request->post['pagseguro_debito_status'])) {
			$data['pagseguro_debito_status'] = $this->request->post['pagseguro_debito_status'];
		} else {
			$data['pagseguro_debito_status'] = $this->config->get('pagseguro_debito_status');
		}
		
		/* Valor minimo debito */
		if (isset($this->request->post['pagseguro_valor_minimo_debito'])) {
			$data['pagseguro_valor_minimo_debito'] = $this->request->post['pagseguro_valor_minimo_debito'];
		} else {
			$data['pagseguro_valor_minimo_debito'] = $this->config->get('pagseguro_valor_minimo_debito');
		}
		
		/* Status de Pagamento */
		$data['statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		/* Zonas Geográficas */
		$data['zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		/* Custom Field */
		$data['custom_fields'] = $this->model_customer_custom_field->getCustomFields();
		
		/* Debug */
		if (file_exists(DIR_LOGS . 'pagseguro.log')) {
			if ((isset($this->request->post['pagseguro_debug']) && $this->request->post['pagseguro_debug'])) {
				$data['debug'] = file(DIR_LOGS . 'pagseguro.log');
			} elseif ($this->config->get('pagseguro_debug')) {
				$data['debug'] = file(DIR_LOGS . 'pagseguro.log');
			} else {
				$data['debug'] = array();
			}
		} else {
			$data['debug'] = array();
		}
		
		/* Links */
		$data['action'] = $this->url->link('extension/payment/pagseguro', 'token=' . $this->session->data['token'], true);
		$data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'], true);
		
        $data['link_custom_field'] = $this->url->link('customer/custom_field', 'token=' . $this->session->data['token'], true);
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$this->response->setOutput($this->load->view('extension/payment/pagseguro.tpl', $data));
	}
	
	public function validate() {
        
        /* Status */
        if ($this->request->post['pagseguro_status']) {
            $this->request->post['pagseguro_desconto_status'] = 1;
            $this->request->post['pagseguro_acrescimo_status'] = 1;
        } else {
            $this->request->post['pagseguro_desconto_status'] = 0;
            $this->request->post['pagseguro_acrescimo_status'] = 0;
        }
        
		/* Error Permission */
		if (!$this->user->hasPermission('modify', 'extension/payment/pagseguro')) {
			$this->error['warning'] = $this->language->get('warning');
		}
		
		/* Error Email */
		if (!filter_var($this->request->post['pagseguro_email'], FILTER_VALIDATE_EMAIL)) {
			$this->error['email'] = $this->language->get('error_email');
		}
		
		/* Error Token */
		if (strlen($this->request->post['pagseguro_token']) < 32) {
			$this->error['token'] = $this->language->get('error_token');
		}
		
		/* Error Quantidade de Parcelas */
		if (!filter_var($this->request->post['pagseguro_qnt_parcelas'], FILTER_VALIDATE_INT)) {
			$this->error['qnt_parcelas'] = $this->language->get('error_qnt_parcela');
		} elseif ($this->request->post['pagseguro_qnt_parcelas'] > 18) {
			$this->error['qnt_parcelas'] = $this->language->get('error_qnt_parcela_invalido');
		}
		
		/* Erorr Quantidade Parcelas sem Juros */
		if (!filter_var($this->request->post['pagseguro_parcelas_sem_juros'], FILTER_VALIDATE_INT)) {
			$this->error['parcelas_sem_juros'] = $this->language->get('error_parcelas_sem_juros');
		} elseif ($this->request->post['pagseguro_parcelas_sem_juros'] > 18) {
			$this->error['parcelas_sem_juros'] = $this->language->get('error_parcelas_sem_juros_invalido');
		}
		
		/* Error Boleto */
		if ($this->request->post['pagseguro_boleto_status']) {
			if (!filter_var($this->request->post['pagseguro_valor_minimo_boleto'], FILTER_VALIDATE_FLOAT)) {
				$this->request->post['pagseguro_valor_minimo_boleto'] = 1.00;
			}
		}
		
		/* Error Cartão de Crédito */
		if ($this->request->post['pagseguro_cartao_status']) {
			if (!filter_var($this->request->post['pagseguro_valor_minimo_cartao'], FILTER_VALIDATE_FLOAT)) {
				$this->request->post['pagseguro_valor_minimo_cartao'] = 1.00;
			}
		}
		
		/* Error Débito */
		if ($this->request->post['pagseguro_debito_status']) {
			if (!filter_var($this->request->post['pagseguro_valor_minimo_debito'], FILTER_VALIDATE_FLOAT)) {
				$this->request->post['pagseguro_valor_minimo_debito'] = 1.00;
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