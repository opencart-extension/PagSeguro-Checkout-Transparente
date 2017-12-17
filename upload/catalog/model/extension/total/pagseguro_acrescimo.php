<?php
class ModelExtensionTotalPagSeguroAcrescimo extends Model {
    
    public function getTotal($total) {
        
        if (isset($this->session->data['pagseguro_acrescimo'])) {
            unset($this->session->data['pagseguro_acrescimo']);
        }
        
        if (!isset($this->session->data['payment_method']['code'])) {
            return false;
        }
        
        $this->language->load('extension/payment/pagseguro');
        
        $acrescimo = 0;
        
        if ($this->session->data['payment_method']['code'] == 'pagseguro_boleto') {            
            if (preg_match('#%#', $this->config->get('payment_pagseguro_acrescimo_boleto'))) {
                $acrescimo = preg_replace('/[^\d\.]/', '', $this->config->get('payment_pagseguro_acrescimo_boleto'));
                
                $acrescimo = (($acrescimo / 100) * $this->cart->getSubTotal());
            } else {
                $acrescimo = $this->config->get('payment_pagseguro_acrescimo_boleto');
            }
        }
        
        if ($this->session->data['payment_method']['code'] == 'pagseguro_cartao') {
            if (preg_match('#%#', $this->config->get('payment_pagseguro_acrescimo_cartao'))) {
                $acrescimo = preg_replace('/[^\d\.]/', '', $this->config->get('payment_pagseguro_acrescimo_cartao'));
                
                $acrescimo = (($acrescimo / 100) * $this->cart->getSubTotal());
            } else {
                $acrescimo = $this->config->get('payment_pagseguro_acrescimo_cartao');
            }
        }
        
        if ($this->session->data['payment_method']['code'] == 'pagseguro_debito') {
            if (preg_match('#%#', $this->config->get('payment_pagseguro_acrescimo_debito'))) {
                $acrescimo = preg_replace('/[^\d\.]/', '', $this->config->get('payment_pagseguro_acrescimo_debito'));
                
                $acrescimo = (($acrescimo / 100) * $this->cart->getSubTotal());
            } else {
                $acrescimo = $this->config->get('payment_pagseguro_acrescimo_debito');
            }
        }
        
        if ($acrescimo > 0) {
            //$order_total =+ $acrescimo;
            
            $this->session->data['pagseguro_acrescimo'] = $acrescimo;
            
            $total['totals'][] = array(
                'code'       => 'pagseguro_acrescimo',
                'title'      => sprintf($this->language->get('text_acrescimo'), $this->currency->format($acrescimo, $this->session->data['currency'])),
                'value'      => $acrescimo,
                'sort_order' => ($this->config->get('sub_total_sort_order') + 1)
            );
            
            $total['total'] += $acrescimo;
        }
    }
}