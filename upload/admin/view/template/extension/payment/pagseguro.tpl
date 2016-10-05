<?php echo $header ?><?php echo $column_left ?>

<div id="content">

  <!-- Page Header -->
  <div class="page-header">
    <div class="container-fluid">

      <div class="pull-right">
        <button type="submit" form="form-moip" data-toggle="tooltip" title="<?php echo $button_save ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="#" data-toggle="tooltip" title="<?php echo $button_cancel ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>	
      </div>

      <h1><?php echo $heading_title ?></h1>
      
      <ul class="breadcrumb">
        <?php foreach($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href'] ?>"><?php echo $breadcrumb['name'] ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>

  <!-- Container -->
  <div class="container-fluid">

    <!-- Error -->
    <?php if ($warning) { ?>
    <div class="alert alert-danger">
      <i class="fa fa-exclamation-circle"></i> <?php echo $warning ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>

    <!-- Panel -->
    <div class="panel panel-default">

      <!-- Title -->
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $heading_title ?></h3>
      </div>

      <!-- Body -->
      <div class="panel-body">

        <!-- Nav -->
        <ul class="nav nav-tabs">
          <li><a data-toggle="tab" href="#config"><?php echo $tab_config ?></a></li>
          <li><a data-toggle="tab" href="#discount"><?php echo $tab_desconto ?></a></li>
          <li><a data-toggle="tab" href="#payment-status"><?php echo $tab_status_pagamento ?></a></li>
          <li><a data-toggle="tab" href="#area"><?php echo $tab_geo_zone ?></a></li>
          <li><a data-toggle="tab" href="#plots"><?php echo $tab_parcelas ?></a></li>
          <li><a data-toggle="tab" href="#payment-method"><?php echo $tab_formas_de_pagamento ?></a></li>
          <li><a data-toggle="tab" href="#debug"><?php echo $tab_debug ?></a></li>
          <li><a data-toggle="tab" href="#doacao"><?php echo $tab_doacao ?></a></li>
        </ul>

        <!-- Form -->
        <form action="<?php echo $action ?>" method="post" enctype="multipart/form-data" id="form-moip" class="form-horizontal">
          <div class="tab-content">
	
            <!-- Tab Config -->
            <div class="tab-pane" id="config">

              <!-- Status -->
              <div class="form-group required">
                <label class="col-sm-2 control-label"><span data-toggle="tooltip" data-html="true" title="<?php echo $help_status ?>"><?php echo $entry_status ?></span></label>
                <div class="col-sm-10">
                  <select name="pagseguro_status" class="form-control">
                  <?php if ($pagseguro_status) { ?>
                  <option value="1" selected><?php echo $text_enabled ?></option>
                  <?php } else { ?>
                  <option value="1"><?php echo $text_enabled ?></option>
                  <?php } ?>
                  
                  <?php if (!$pagseguro_status) { ?>
                  <option value="0" selected><?php echo $text_disabled ?></option>
                  <?php } else { ?>
                  <option value="0"><?php echo $text_disabled ?></option>
                  <?php } ?>
                  </select>
                </div>
              </div>

              <!-- Email -->
              <div class="form-group required">
               <label class="col-sm-2 control-label"><span data-toggle="tooltip" data-html="true" title="<?php echo $help_email ?>"><?php echo $entry_email ?></span></label>
                <div class="col-sm-10">
                  <input name="pagseguro_email" type="text" class="form-control" value="<?php echo $pagseguro_email ?>" />
                  <?php if ($error_email) { ?>
                  <div class="text-danger"><?php echo $error_email ?></div>
                  <?php } ?>
                </div>
              </div>

              <!-- Token -->
              <div class="form-group required">
                <label class="col-sm-2 control-label"><span data-toggle="tooltip" data-html="true" title="<?php echo $help_token ?>"><?php echo $entry_token ?></span></label>
                <div class="col-sm-10">
                  <input name="pagseguro_token" type="text" class="form-control" value="<?php echo $pagseguro_token ?>" />
                  <?php if ($error_token) { ?>
                  <div class="text-danger"><?php echo $error_token ?></div>
                  <?php } ?>
                </div>
              </div>

              <!-- Modo Teste -->
              <div class="form-group required">
                <label class="col-sm-2 control-label"><span data-toggle="tooltip" data-html="true" title="<?php echo $help_modo_teste ?>"><?php echo $entry_modo_teste ?></span></label>
                <div class="col-sm-10">
                  <select name="pagseguro_modo_teste" class="form-control">
                    <?php if ($pagseguro_modo_teste) { ?>
                    <option value="1" selected><?php echo $text_yes ?></option>
                    <?php } else { ?>
                    <option value="1"><?php echo $text_yes ?></option>
                    <?php } ?>
                    
                    
                    <?php if (!$pagseguro_modo_teste) { ?>
                    <option value="0" selected><?php echo $text_no ?></option>
                    <?php } else { ?>
                    <option value="0"><?php echo $text_no ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>

              <!-- Debug -->
              <div class="form-group required">
                <label class="col-sm-2 control-label"><span data-toggle="tooltip" data-html="true" title="<?php echo $help_debug ?>"><?php echo $entry_debug ?></span></label>
                <div class="col-sm-10">
                  <select name="pagseguro_debug" class="form-control">
                    <?php if ($pagseguro_debug) { ?>
                    <option value="1" selected><?php echo $text_yes ?></option>
                    <?php } else { ?>
                    <option value="1"><?php echo $text_yes ?></option>
                    <?php } ?>
                    
                    
                    <?php if (!$pagseguro_debug) { ?>
                    <option value="0" selected><?php echo $text_no ?></option>
                    <?php } else { ?>
                    <option value="0"><?php echo $text_no ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>

              <!-- Notificar Cliente -->
              <div class="form-group required">
                <label class="col-sm-2 control-label"><span data-toggle="tooltip" data-html="true" title="<?php echo $help_notificar_cliente ?>"><?php echo $entry_notificar_cliente ?></span></label>
                <div class="col-sm-10">
                  <select name="pagseguro_notificar_cliente" class="form-control">
                    <?php if ($pagseguro_notificar_cliente) { ?>
                    <option value="1" selected><?php echo $text_yes ?></option>
                    <?php } else { ?>
                    <option value="1"><?php echo $text_yes ?></option>
                    <?php } ?>
                    
                    
                    <?php if (!$pagseguro_notificar_cliente) { ?>
                    <option value="0" selected><?php echo $text_no ?></option>
                    <?php } else { ?>
                    <option value="0"><?php echo $text_no ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>

              <!-- Custom Field (Número) -->
              <div class="form-group required">
                <label class="col-sm-2 control-label"><span data-toggle="tooltip" data-html="true" title="<?php echo $help_numero ?>"><?php echo $entry_numero ?></span></label>
                <div class="col-sm-10">
                  <span class="input-group">
                    <select name="pagseguro_numero_residencia" class="form-control">
                    <?php foreach($custom_fields as $custom_field) { ?>
                    <?php if ($pagseguro_numero_residencia == $custom_field['custom_field_id']) { ?>
                    <option value="<?php echo $custom_field['custom_field_id'] ?>" selected><?php echo $custom_field['name'] ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $custom_field['custom_field_id'] ?>"><?php echo $custom_field['name'] ?></option>
                    <?php } ?>
                    <?php } ?>
                    </select>
                    
                    <span class="input-group-btn">
                      <a href="<?php echo $link_custom_field ?>" class="btn btn-primary"><?php echo $text_custom_field ?></a>
                    </span>
                  </span>
                </div>
              </div>

              <!-- Custom Field (Data de Nascimento) -->
              <div class="form-group required">
                <label class="col-sm-2 control-label"><span data-toggle="tooltip" data-html="true" title="<?php echo $help_data_nascimento ?>"><?php echo $entry_data_nascimento ?></span></label>
                <div class="col-sm-10">
                  <span class="input-group">
                    <select name="pagseguro_data_nascimento" class="form-control">
                    <?php foreach($custom_fields as $custom_field) { ?>
                    <?php if ($pagseguro_data_nascimento == $custom_field['custom_field_id']) { ?>
                    <option value="<?php echo $custom_field['custom_field_id'] ?>" selected><?php echo $custom_field['name'] ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $custom_field['custom_field_id'] ?>"><?php echo $custom_field['name'] ?></option>
                    <?php } ?>
                    <?php } ?>
                    </select>
                    
                    <span class="input-group-btn">
                      <a href="<?php echo $link_custom_field ?>" class="btn btn-primary"><?php echo $text_custom_field ?></a>
                    </span>
                  </span>
                </div>
              </div>

              <!-- Custom Field (CPF) -->
              <div class="form-group required">
                <label class="col-sm-2 control-label"><span data-toggle="tooltip" data-html="true" title="<?php echo $help_cpf ?>"><?php echo $entry_cpf ?></span></label>
                <div class="col-sm-10">
                  <span class="input-group">
                    <select name="pagseguro_cpf" class="form-control">
                    <?php foreach($custom_fields as $custom_field) { ?>
                    <?php if ($pagseguro_cpf == $custom_field['custom_field_id']) { ?>
                    <option value="<?php echo $custom_field['custom_field_id'] ?>" selected><?php echo $custom_field['name'] ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $custom_field['custom_field_id'] ?>"><?php echo $custom_field['name'] ?></option>
                    <?php } ?>
                    <?php } ?>
                    </select>
                    
                    <span class="input-group-btn">
                      <a href="<?php echo $link_custom_field ?>" class="btn btn-primary"><?php echo $text_custom_field ?></a>
                    </span>
                  </span>
                </div>
              </div>
			  
              <!-- URL de Retorno -->
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_url_retorno ?></label>
                <div class="col-sm-10">
                  <input type="text" disabled value="https://www.MYSTORE.com.br/index.php?route=payment/pagseguro/callback" class="form-control" />
                </div>
              </div>
            </div>

            <!-- Tab Discount -->
            <div class="tab-pane" id="discount">

              <!-- Descontos -->
              <fieldset>
                <legend><?php echo $text_desconto ?></legend>

                <!-- Boleto -->
                <div class="form-group">
                  <label class="col-sm-2 control-label"><span data-toggle="tooltip" data-html="true" title="<?php echo $help_exemplo_desconto ?>"><?php echo $entry_desconto_boleto ?></span></label>
                  <div class="col-sm-10">
                    <input type="text" name="pagseguro_desconto_boleto" value="<?php echo $pagseguro_desconto_boleto ?>" class="form-control" />
                  </div>
                </div>

                <!-- Cartão de Crédito -->
                <div class="form-group">
                  <label class="col-sm-2 control-label"><span data-toggle="tooltip" data-html="true" title="<?php echo $help_exemplo_desconto ?>"><?php echo $entry_desconto_cartao ?></span></label>
                  <div class="col-sm-10">
                    <input type="text" name="pagseguro_desconto_cartao" value="<?php echo $pagseguro_desconto_cartao ?>" class="form-control" />
                  </div>
                </div>

                <!-- Débito -->
                <div class="form-group">
                  <label class="col-sm-2 control-label"><span data-toggle="tooltip" data-html="true" title="<?php echo $help_exemplo_desconto ?>"><?php echo $entry_desconto_debito ?></span></label>
                  <div class="col-sm-10">
                    <input type="text" name="pagseguro_desconto_debito" value="<?php echo $pagseguro_desconto_debito ?>" class="form-control" />
                  </div>
                </div>
              </fieldset>

              <!-- Acréscimo -->
              <fieldset>
                <legend><?php echo $text_acrescimo ?></legend>

                <!-- Boleto -->
                <div class="form-group">
                  <label class="col-sm-2 control-label"><span data-toggle="tooltip" data-html="true" title="<?php echo $help_exemplo_desconto ?>"><?php echo $entry_acrescimo_boleto ?></span></label>
                  <div class="col-sm-10">
                    <input type="text" name="pagseguro_acrescimo_boleto" value="<?php echo $pagseguro_acrescimo_boleto ?>" class="form-control" />
                  </div>
                </div>

                <!-- Cartão -->
                <div class="form-group">
                  <label class="col-sm-2 control-label"><span data-toggle="tooltip" data-html="true" title="<?php echo $help_exemplo_desconto ?>"><?php echo $entry_acrescimo_cartao ?></span></label>
                  <div class="col-sm-10">
                    <input type="text" name="pagseguro_acrescimo_cartao" value="<?php echo $pagseguro_acrescimo_cartao ?>" class="form-control" />
                  </div>
                </div>

                <!-- Débito -->
                <div class="form-group">
                  <label class="col-sm-2 control-label"><span data-toggle="tooltip" data-html="true" title="<?php echo $help_exemplo_desconto ?>"><?php echo $entry_acrescimo_debito ?></span></label>
                  <div class="col-sm-10">
                    <input type="text" name="pagseguro_acrescimo_debito" value="<?php echo $pagseguro_acrescimo_debito ?>" class="form-control" />
                  </div>
                </div>
              </fieldset>
            </div>

            <!-- Tab Status de Pagamento -->
            <div class="tab-pane" id="payment-status">
            
              <!-- Aguardando Pagamento -->
              <div class="form-group">
                <label class="col-sm-2 control-label"><span data-toggle="tooltip" data-html="true" title="<?php echo $help_aguardando_pagamento ?>"><?php echo $entry_aguardando_pagamento ?></span></label>
                <div class="col-sm-10">
                  <select name="pagseguro_aguardando_pagamento" class="form-control">
                  <?php foreach($statuses as $status) { ?>
                  <?php if ($pagseguro_aguardando_pagamento == $status['order_status_id']) { ?>
                  <option value="<?php echo $status['order_status_id'] ?>" selected><?php echo $status['name'] ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $status['order_status_id'] ?>"><?php echo $status['name'] ?></option>
                  <?php } ?>
                  <?php } ?>
                  </select>
                </div>
              </div>
              
              <!-- Em Análise -->
              <div class="form-group">
                <label class="col-sm-2 control-label"><span data-toggle="tooltip" data-html="true" title="<?php echo $help_analise ?>"><?php echo $entry_analise ?></span></label>
                <div class="col-sm-10">
                  <select name="pagseguro_analise" class="form-control">
                    <?php foreach($statuses as $status) { ?>
                    <?php if ($pagseguro_analise == $status['order_status_id']) { ?>
                    <option value="<?php echo $status['order_status_id'] ?>" selected><?php echo $status['name'] ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $status['order_status_id'] ?>"><?php echo $status['name'] ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
              
              <!-- Pago -->
              <div class="form-group">
                <label class="col-sm-2 control-label"><span data-toggle="tooltip" data-html="true" title="<?php echo $help_pago ?>"><?php echo $entry_pago ?></span></label>
                <div class="col-sm-10">
                  <select name="pagseguro_paga" class="form-control">
                    <?php foreach($statuses as $status) { ?>
                    <?php if ($pagseguro_paga == $status['order_status_id']) { ?>
                    <option value="<?php echo $status['order_status_id'] ?>" selected><?php echo $status['name'] ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $status['order_status_id'] ?>"><?php echo $status['name'] ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
              
              <!-- Disponível -->
              <div class="form-group">
                <label class="col-sm-2 control-label"><span data-toggle="tooltip" data-html="true" title="<?php echo $help_disponivel ?>"><?php echo $entry_disponivel ?></span></label>
                <div class="col-sm-10">
                  <select name="pagseguro_disponivel" class="form-control">
                    <?php foreach($statuses as $status) { ?>
                    <?php if ($pagseguro_disponivel == $status['order_status_id']) { ?>
                    <option value="<?php echo $status['order_status_id'] ?>" selected><?php echo $status['name'] ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $status['order_status_id'] ?>"><?php echo $status['name'] ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
              
              <!-- Disputa -->
              <div class="form-group">
                <label class="col-sm-2 control-label"><span data-toggle="tooltip" data-html="true" title="<?php echo $help_disputa ?>"><?php echo $entry_disputa ?></span></label>
                <div class="col-sm-10">
                  <select name="pagseguro_disputa" class="form-control">
                    <?php foreach($statuses as $status) { ?>
                    <?php if ($pagseguro_disputa == $status['order_status_id']) { ?>
                    <option value="<?php echo $status['order_status_id'] ?>" selected><?php echo $status['name'] ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $status['order_status_id'] ?>"><?php echo $status['name'] ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
              
              <!-- Devolvida -->
              <div class="form-group">
                <label class="col-sm-2 control-label"><span data-toggle="tooltip" data-html="true" title="<?php echo $help_devolvida ?>"><?php echo $entry_devolvido ?></span></label>
                <div class="col-sm-10">
                  <select name="pagseguro_devolvida" class="form-control">
                    <?php foreach($statuses as $status) { ?>
                    <?php if ($pagseguro_devolvida == $status['order_status_id']) { ?>
                    <option value="<?php echo $status['order_status_id'] ?>" selected><?php echo $status['name'] ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $status['order_status_id'] ?>"><?php echo $status['name'] ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
              
              <!-- Cancelada -->
              <div class="form-group">
                <label class="col-sm-2 control-label"><span data-toggle="tooltip" data-html="true" title="<?php echo $entry_cancelada ?>"><?php echo $entry_cancelada ?></span></label>
                <div class="col-sm-10">
                  <select name="pagseguro_cancelada" class="form-control">
                   <?php foreach($statuses as $status) { ?>
                   <?php if ($pagseguro_cancelada == $status['order_status_id']) { ?>
                   <option value="<?php echo $status['order_status_id'] ?>" selected><?php echo $status['name'] ?></option>
                   <?php } else { ?>
                   <option value="<?php echo $status['order_status_id'] ?>"><?php echo $status['name'] ?></option>
                   <?php } ?>
                   <?php } ?>
                  </select>
                </div>
              </div>
            </div>

            <!-- Tab Área Geográfica e Ordem -->
            <div class="tab-pane" id="area">
            
              <!-- Zona Geográfica -->
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_geo_zone ?></label>
                <div class="col-sm-10">
                  <select name="pagseguro_geo_zone" class="form-control">
                    <option value="0"><?php echo $text_all_zones ?></option>
                    <?php foreach($zones as $zone) { ?>
                    <?php if ($pagseguro_geo_zone == $zone['geo_zone_id']) { ?>
                    <option value="<?php echo $zone['geo_zone_id'] ?>" selected><?php echo $zone['name'] ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $zone['geo_zone_id'] ?>"><?php echo $zone['name'] ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
            
              <!-- Sort Order -->
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_sort_order ?></label>
                <div class="col-sm-10">
                  <input type="text" name="pagseguro_sort_order" value="<?php echo $pagseguro_sort_order ?>" class="form-control" />
                </div>
              </div>
            </div>

            <!-- Tab Parcelas -->
            <div class="tab-pane" id="plots">
            
              <!-- Quantidade Total de Parcelas -->
              <div class="form-group required">
                <label class="col-sm-2 control-label"><span data-toggle="tooltip" data-html="true" title="<?php echo $help_exemplo_parcela ?>"><?php echo $entry_qnt_parcelas ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="pagseguro_qnt_parcelas" value="<?php echo $pagseguro_qnt_parcelas ?>" class="form-control" />
                  <?php if ($error_qnt_parcela) { ?>
                  <div class="text-danger"><?php echo $error_qnt_parcela ?></div>
                  <?php } ?>
                </div>
              </div>
              
              <!-- Quantidade de Parcelas sem Juros -->
              <div class="form-group required">
                <label class="col-sm-2 control-label"><span data-toggle="tooltip" data-html="true" title="<?php echo $help_parcela_sem_juros ?>"><?php echo $entry_parcelas_sem_juros ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="pagseguro_parcelas_sem_juros" value="<?php echo $pagseguro_parcelas_sem_juros ?>" class="form-control" />
                  <?php if ($error_parcelas_sem_juros) { ?>
                  <div class="text-danger"><?php echo $error_parcelas_sem_juros ?></div>
                  <?php } ?>
                </div>
              </div>
            </div>

            <!-- Tab Métodos de Pagamento -->
            <div class="tab-pane" id="payment-method">
            
              <!-- Boleto -->
              <fieldset>
                <legend><?php echo $text_boleto ?></legend>
                
                
                <!-- Status Boleto -->
                <div class="form-group">
                  <label class="col-sm-2 control-label"><?php echo $entry_status ?></label>
                  <div class="col-sm-10">
                    <select name="pagseguro_boleto_status" class="form-control">
                      <?php if ($pagseguro_boleto_status) { ?>
                      <option value="1" selected><?php echo $text_enabled ?></option>
                      <?php } else { ?>
                      <option value="1"><?php echo $text_enabled ?></option>
                      <?php } ?>
                      
                      
                      <?php if (!$pagseguro_boleto_status) { ?>
                      <option value="0" selected><?php echo $text_disabled ?></option>
                      <?php } else { ?>
                      <option value="0"><?php echo $text_disabled ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
                
                <!-- Valor Mínimo para Boleto -->
                <div class="form-group">
                  <label class="col-sm-2 control-label"><?php echo $entry_valor_minimo ?></label>
                  <div class="col-sm-10">
                    <input type="text" name="pagseguro_valor_minimo_boleto" value="<?php echo $pagseguro_valor_minimo_boleto ?>" class="form-control" />
                  </div>
                </div>
              </fieldset>
            
              <!-- Cartão de Crédito -->
              <fieldset>
                <legend><?php echo $text_cartao ?></legend>
                
                <!-- Status Cartão de Crédito -->
                <div class="form-group">
                  <label class="col-sm-2 control-label"><?php echo $entry_status ?></label>
                  <div class="col-sm-10">
                    <select name="pagseguro_cartao_status" class="form-control">
                      <?php if ($pagseguro_cartao_status) { ?>
                      <option value="1" selected><?php echo $text_enabled ?></option>
                      <?php } else { ?>
                      <option value="1"><?php echo $text_enabled ?></option>
                      <?php } ?>
                      
                      
                      <?php if (!$pagseguro_cartao_status) { ?>
                      <option value="0" selected><?php echo $text_disabled ?></option>
                      <?php } else { ?>
                      <option value="0"><?php echo $text_disabled ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
            
                <!-- Valor mínimo para cartão de crédito -->
                <div class="form-group">
                  <label class="col-sm-2 control-label"><?php echo $entry_valor_minimo ?></label>
                  <div class="col-sm-10">
                    <input type="text" name="pagseguro_valor_minimo_cartao" value="<?php echo $pagseguro_valor_minimo_cartao ?>" class="form-control" />
                  </div>
                </div>
              </fieldset>
            
              <!-- Débito -->
              <fieldset>
                <legend><?php echo $text_debito ?></legend>
              
                <!-- Status débito -->
                <div class="form-group">
                  <label class="col-sm-2 control-label"><?php echo $entry_status ?></label>
                  <div class="col-sm-10">
                    <select name="pagseguro_debito_status" class="form-control">
                      <?php if ($pagseguro_debito_status) { ?>
                      <option value="1" selected><?php echo $text_enabled ?></option>
                      <?php } else { ?>
                      <option value="1"><?php echo $text_enabled ?></option>
                      <?php } ?>
                      
                      
                      <?php if (!$pagseguro_debito_status) { ?>
                      <option value="0" selected><?php echo $text_disabled ?></option>
                      <?php } else { ?>
                      <option value="0"><?php echo $text_disabled ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
              
                <!-- Valor mínimo para débito -->
                <div class="form-group">
                  <label class="col-sm-2 control-label"><?php echo $entry_valor_minimo ?></label>
                  <div class="col-sm-10">
                    <input type="text" name="pagseguro_valor_minimo_debito" value="<?php echo $pagseguro_valor_minimo_debito ?>" class="form-control" />
                  </div>
                </div>
              </fieldset> <!-- /Fieldset Débito -->
            </div> <!-- /#payment-method -->
          
            <!-- Tab Debug -->
            <div class="tab-pane" id="debug">
              <div class="alert alert-info">
                <h3>Erros Comuns</h3>
                <p>http://forum.pagseguro.uol.com.br/t/8514331/msg-de-erro-como-pagseguroserviceexception-http-401---unauthorized</p>
                <p>http://forum.pagseguro.uol.com.br/t/9454406/checkout-transparente-forbidden</p>
                <p>http://forum.pagseguro.uol.com.br/t/11286061/erro-ao-finalizar-pedido---forbidden</p>
                <p>http://forum.pagseguro.uol.com.br/t/10184598/forbidden-ao-tentar-autenticar-na-api</p>
                <p>http://forum.pagseguro.uol.com.br/t/9391115/erro-no-retorno-da-consulta-por-transacao</p>
                <button class="close" data-dismiss="alert" type="button">&times;</button>
              </div>
              <div class="well" style="min-height:150px">
                <?php
                  foreach($debug as $value) {
                    echo htmlspecialchars($value) . '<br/>';
                  }
                ?>
              </div>
            </div>
          
            <!-- Tab Doação -->
            <div class="tab-pane" id="doacao">
              <div class="col-sm-6">
                <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=5262W5FHDE6KA" target="_blank">
                  <img src="https://www.paypalobjects.com/pt_BR/BR/i/btn/btn_donateCC_LG.gif" alt="Contribua" title="Contribua" />
                </a>
              </div>
              <div class="col-sm-6">
                <a href="https://pagseguro.uol.com.br/checkout/v2/donation.html?currency=BRL&receiverEmail=valdeirpsr@hotmail.com.br" target="_blank">
                  <img src="https://p.simg.uol.com.br/out/pagseguro/i/botoes/doacoes/209x48-doar-assina.gif" alt="Contribua" title="Contribua" />
                </a>
              </div>
            </div>
          </div><!-- /.tab-content -->
        </form> <!-- /Form -->
      </div><!-- /.panel-body -->
    </div><!-- /.panel.panel-default -->
  </div><!-- /.container-fluid -->
</div><!-- /#content -->

<script type="text/javascript">
<?php if (empty($pagseguro_token)) { ?>
$('.nav-tabs li:first').addClass('active');
$('.tab-content div:first').addClass('active');
<?php } else { ?>
$('.nav-tabs li:last').addClass('active');
$('#doacao').addClass('active');
<?php } ?>

$('fieldset legend').css('cursor', 'pointer');
$('fieldset').css('margin-bottom', 30);

$('fieldset legend').click(function(){
	$(this).parent().find('div').slideToggle('slow');
});
</script>

<?php echo $footer ?>