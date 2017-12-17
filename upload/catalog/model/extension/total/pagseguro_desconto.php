<?php
class ModelExtensionTotalPagSeguroDesconto extends Model {
    
    public function getTotal($total) {
        
        if (isset($this->session->data['pagseguro_desconto'])) {
            unset($this->session->data['pagseguro_desconto']);
        }
        
        if (!isset($this->session->data['payment_method']['code'])) {
            return false;
        }
        
        $this->language->load('extension/payment/pagseguro');
        
        $desconto = 0;
        
        if ($this->session->data['payment_method']['code'] == 'pagseguro_boleto') {            
            if (preg_match('#%#', $this->config->get('payment_pagseguro_desconto_boleto'))) {
                $desconto = preg_replace('/[\D\.]/', '', $this->config->get('payment_pagseguro_desconto_boleto'));
                
                $desconto = (($desconto / 100) * $this->cart->getSubTotal());
            } else {
                $desconto = $this->config->get('payment_pagseguro_desconto_boleto');
            }
        }
        
        if ($this->session->data['payment_method']['code'] == 'pagseguro_cartao') {
            if (preg_match('#%#', $this->config->get('payment_pagseguro_desconto_cartao'))) {
                $desconto = preg_replace('/[\D\.]/', '', $this->config->get('payment_pagseguro_desconto_cartao'));
                
                $desconto = (($desconto / 100) * $this->cart->getSubTotal());
            } else {
                $desconto = $this->config->get('payment_pagseguro_desconto_cartao');
            }
        }
        
        if ($this->session->data['payment_method']['code'] == 'pagseguro_debito') {
            if (preg_match('#%#', $this->config->get('payment_pagseguro_desconto_debito'))) {
                $desconto = preg_replace('/[\D\.]/', '', $this->config->get('payment_pagseguro_desconto_debito'));
                
                $desconto = (($desconto / 100) * $this->cart->getSubTotal());
            } else {
                $desconto = $this->config->get('payment_pagseguro_desconto_debito');
            }
        }
        
        if ($desconto > 0) {
            //$total =- $desconto;
            
            $this->session->data['pagseguro_desconto'] = $desconto;
            
            $total['totals'][] = array(
                'code'       => 'pagseguro_desconto',
                'title'      => sprintf($this->language->get('text_desconto'), $this->currency->format($desconto, $this->session->data['currency'])),
                'value'      => -$desconto,
                'sort_order' => ($this->config->get('sub_total_sort_order') + 1)
            );
            
            $total['total'] -= $desconto;
        }
    }
}