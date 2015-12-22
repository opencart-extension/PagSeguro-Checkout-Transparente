<?php
class ModelTotalPagSeguroAcrescimo extends Model {
    
    public function getTotal(&$total_data, &$total, &$taxes) {
        
        if (!isset($this->session->data['payment_method']['code'])) {
            return false;
        }
        
        $this->language->load('payment/pagseguro');
        
        $acrescimo = 0;
        
        if ($this->session->data['payment_method']['code'] == 'pagseguro_boleto') {            
            if (preg_match('#%#', $this->config->get('pagseguro_acrescimo_boleto'))) {
                $acrescimo = preg_replace('/[^\d\.]/', '', $this->config->get('pagseguro_acrescimo_boleto'));
                
                $acrescimo = (($acrescimo / 100) * $this->cart->getSubTotal());
            } else {
                $acrescimo = $this->config->get('pagseguro_acrescimo_boleto');
            }
        }
        
        if ($this->session->data['payment_method']['code'] == 'pagseguro_credito') {
            if (preg_match('#%#', $this->config->get('pagseguro_acrescimo_cartao'))) {
                $acrescimo = preg_replace('/[^\d\.]/', '', $this->config->get('pagseguro_acrescimo_cartao'));
                
                $acrescimo = (($acrescimo / 100) * $this->cart->getSubTotal());
            } else {
                $acrescimo = $this->config->get('pagseguro_acrescimo_cartao');
            }
        }
        
        if ($this->session->data['payment_method']['code'] == 'pagseguro_debito') {
            if (preg_match('#%#', $this->config->get('pagseguro_acrescimo_debito'))) {
                $acrescimo = preg_replace('/[^\d\.]/', '', $this->config->get('pagseguro_acrescimo_debito'));
                
                $acrescimo = (($acrescimo / 100) * $this->cart->getSubTotal());
            } else {
                $acrescimo = $this->config->get('pagseguro_acrescimo_debito');
            }
        }
        
        if ($acrescimo > 0) {
            $total += $acrescimo;
            
            $total_data[] = array(
                'code'       => 'pagseguro_desconto',
                'title'      => sprintf($this->language->get('text_acrescimo'), $this->currency->format($acrescimo)),
                'value'      => +$acrescimo,
                'sort_order' => ($this->config->get('total_sort_order') - 1)
            );
        }
    }
}