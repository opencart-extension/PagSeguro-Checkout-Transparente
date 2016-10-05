<?php
//Heading
$_['heading_title'] = 'PagSeguro';

//Text
$_['text_pagseguro']       = '<img src="view/image/payment/pagseguro.png" />';
$_['text_success']         = 'Módulo atualizado com sucesso.';
$_['text_payment']         = 'Formas de Pagamento';
$_['text_desconto']        = 'Desconto';
$_['text_acrescimo']       = 'Acréscimo';
$_['text_boleto']          = 'Boleto';
$_['text_cartao']          = 'Cartão de Crédito';
$_['text_debito']          = 'Débito';
$_['text_custom_field']    = 'Criar campo';

//Tab
$_['tab_config']              = 'Config';
$_['tab_desconto']            = 'Desc. e Acrés.';
$_['tab_status_pagamento']    = 'Status de Pagamento';
$_['tab_geo_zone']            = 'Área Geográfica';
$_['tab_parcelas']            = 'Parcela';
$_['tab_formas_de_pagamento'] = 'Métodos de Pagamento';
$_['tab_debug'] 			  = 'Debug';
$_['tab_doacao'] 			  = 'Doação';

//Entry
$_['entry_status']               = 'Situação:';
$_['entry_email']                = 'E-mail:';
$_['entry_token']                = 'Token:';
$_['entry_modo_teste']           = 'Modo de Teste:';
$_['entry_debug']	             = 'Debug:';
$_['entry_notificar_cliente']    = 'Notificar Cliente:';
$_['entry_url_retorno']          = 'URL de Retorno:';
$_['entry_numero']               = 'Número:';
$_['entry_data_nascimento']      = 'Data de Nascimento:';
$_['entry_cpf']                  = 'CPF:';
$_['entry_desconto_boleto']      = 'Desconto Boleto:';
$_['entry_desconto_cartao']      = 'Desconto Cartão de Crédito:';
$_['entry_desconto_debito']      = 'Desconto Débito:';
$_['entry_acrescimo_boleto']     = 'Acréscimo Boleto:';
$_['entry_acrescimo_cartao']     = 'Acréscimo Cartão de Crédito:';
$_['entry_acrescimo_debito']     = 'Acréscimo Débito:';
$_['entry_aguardando_pagamento'] = 'Aguardando Pagamento';
$_['entry_analise']              = 'Em Análise';
$_['entry_pago']                 = 'Pago';
$_['entry_disponivel']           = 'Disponível';
$_['entry_disputa']              = 'Em Disputa';
$_['entry_devolvido']            = 'Devolvido';
$_['entry_cancelada']            = 'Cancelada';
$_['entry_geo_zone']             = 'Zona Geográfica';
$_['entry_sort_order']           = 'Ordem';
$_['entry_qnt_parcelas']         = 'Quantidade de Parcelas';
$_['entry_parcelas_sem_juros']   = 'Parcelas sem juros';
$_['entry_valor_minimo']         = 'Valor mínimo';

//Help
$_['help_status']               = 'Habilite ou Desabilite o módulo (Essa opção não habilitará/desabilitará os métodos de pagamento)';
$_['help_email']                = 'E-mail do PagSeguro';
$_['help_token']                = 'Token de Segurança. Caso não tenha entre em contato com o suporte do PagSeguro';
$_['help_modo_teste']           = 'É obrigatório que a CONTA TESTE seja qualquer-coisa@SANDBOX.PAGSEGURO.COM.BR';
$_['help_debug']           		= 'Salva em um arquivo de log todos os dados das requisições';
$_['help_notificar_cliente']    = 'Notifica o cliente em caso de atualização da situação do pagamento';
$_['help_numero']               = 'Informe o campo (Custom Field) responsável pelo armazenamento do número da residência.';
$_['help_data_nascimento']      = 'Informe o campo (Custom Field) responsável pelo armazenamento da data de nascimento do cliente.';
$_['help_cpf']                  = 'Informe o campo (Custom Field) responsável pelo armazenamento do número de CPF do cliente.';
$_['help_exemplo_desconto']     = 'Ex: 18.00 ou 18%';
$_['help_aguardando_pagamento'] = 'O comprador iniciou a transação, mas até o momento o PagSeguro não recebeu nenhuma informação sobre o pagamento.';
$_['help_analise']              = 'O comprador optou por pagar com um cartão de crédito e o PagSeguro está analisando o risco da transação.';
$_['help_pago']                 = 'A transação foi paga pelo comprador e o PagSeguro já recebeu uma confirmação da instituição financeira responsável pelo processamento.';
$_['help_disponivel']           = 'A transação foi paga e chegou ao final de seu prazo de liberação sem ter sido retornada e sem que haja nenhuma disputa aberta.';
$_['help_disputa']              = 'O comprador, dentro do prazo de liberação da transação, abriu uma disputa.';
$_['help_devolvida']            = 'O valor da transação foi devolvido para o comprador.';
$_['help_cancelada']            = '';
$_['help_exemplo_parcela']      = 'Máximo: 18';
$_['help_parcela_sem_juros']    = 'Mínimo: 2';

//Error
$_['warning']                           = 'Você não tem permissão para modificar esse módulo';
$_['error_email']                       = 'E-mail Inválido';
$_['error_token']                       = 'Token Inválido';
$_['error_qnt_parcela']                 = 'Campo Inválido';
$_['error_qnt_parcela_invalido']        = 'O máximo deverá ser 18 parcelas';
$_['error_parcelas_sem_juros']          = 'Campo Inválido';
$_['error_parcelas_sem_juros_invalido'] = 'O máximo deverá ser 18 parcelas';