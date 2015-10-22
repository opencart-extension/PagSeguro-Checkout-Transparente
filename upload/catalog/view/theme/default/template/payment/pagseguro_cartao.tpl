<style type="text/css">
  .overlay {height: 30px;width: 68px;background: #FFF;position: absolute;opacity: 0.7;}
  .overlay:hover {opacity: 0;}
  .vhide, .vlhide {display:none}
</style>

<div class="container">
  <div class="row-fluid">
    <div class="alert alert-danger vlhide" id="warning" role="alert"></div>
    
    <?php if (strlen($session_id) != 32) { ?>
    <div class="alert alert-danger" id="warning" role="alert"><?php echo $session_id ?></div>
    <?php exit(); } ?>

    <div class="form-horizontal">
      <div class="form-group">
        <div id="bandeiras" class="col-sm-7 col-sm-offset-2"></div>
      </div>

      <div id="form" class="col-sm-offset-2">
        <div class="form-group">
          <label class="col-sm-2 control-label">Nome:</label>
          <div class="col-sm-5">
            <input class="form-control" type="text" id="nome" name="nome" placeholder="Ex: Valdeir Santana" />
            <input type="hidden" id="bandeira" name="bandeira" />
          </div>
        </div>

        <div class="form-group">
          <label class="col-sm-2 control-label">Data de Nascimento:</label>
          <div class="col-sm-5">
            <input class="form-control" type="text" id="data-nascimento" name="data-nascimento" placeholder="Ex: 13/07/1993" />
          </div>
        </div>

        <div class="form-group">
          <label class="col-sm-2 control-label">CPF:</label>
          <div class="col-sm-5">
            <input class="form-control" type="text" id="cpf" name="cpf" placeholder="Ex: 222.222.222-22" />
          </div>
        </div>

        <div class="form-group">
          <label class="col-sm-2 control-label">Telefone:</label>
          <div class="col-sm-5">
            <input class="form-control" type="text" id="telefone" name="telefone" placeholder="Ex: (11)98765-4321" />
          </div>
        </div>
        
        <div class="form-group">
          <label class="col-sm-2 control-label">Número do Cartão:</label>
          <div class="col-sm-5">
            <input class="form-control" type="text" id="numero-cartao" name="numero-cartao" />
          </div>
        </div>
        
        <div class="form-group">
          <label class="col-sm-2 control-label">Validade:</label>
          <div class="col-sm-5">
            <input class="form-control" type="text" id="validade" name="validade" placeholder="Ex: 12/2015" />
          </div>
        </div>

        <div class="form-group">
          <label class="col-sm-2 control-label">Código de Segurança:</label>
          <div class="col-sm-5">
            <input class="form-control" type="text" id="cvv" name="cvv" placeholder="Ex: 123 ou 1234" />
          </div>
        </div>
        
        <div class="alert alert-info alert-info-installments fade">Carregando...</div>
        
        <div class="form-group vhide">
          <label class="col-sm-2 control-label">Parcelas:</label>
          <div class="col-sm-5">
            <select class="form-control" id="parcelas" name="parcelas"></select>
          </div>
        </div>
        
        <div class="form-group vhide">
          <div class="col-sm-5 col-sm-offset-2">
            <button type="button" id="button-confirm" class="btn btn-primaty">Pagar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
	PagSeguroDirectPayment.setSessionId('<?php echo $session_id ?>');
	
	PagSeguroDirectPayment.getPaymentMethods({
		success: function(bandeiras){
			var cards = bandeiras.paymentMethods.CREDIT_CARD.options;
			
			$.map(cards, function(e){
				$('#bandeiras').append('<a class="pull-left" id="' + e.name + '"><div class="overlay"></div><img src="https://stc.pagseguro.uol.com.br' + e.images.MEDIUM.path + '" /></a>');
			});
			
			getBrand();
		}
	});
	
	var getBrand = function(){
		$('#numero-cartao').keyup(function(){
			if ($(this).val().length >= 6) {
        $('.alert-info-installments').addClass('in');
				PagSeguroDirectPayment.getBrand({
					cardBin: $(this).val(),
					success: function(card){
						$('#bandeiras').find('.overlay').css('opacity', '0.7');
						$('#bandeiras #' + card.brand.name.toUpperCase()).find('.overlay').css('opacity', 0);
						$('#bandeira').val(card.brand.name);
            $('.alert-info-installments').addClass('out').remove();
						getInstallments(card.brand.name);
					}
				});
			}
		});
	};
	
	var getInstallments = function(brand){
		PagSeguroDirectPayment.getInstallments({
			amount: <?php echo $total ?>, //Valor do pedido
			maxInstallmentNoInterest: <?php echo $max_parcelas_sem_juros ?>, //Qnt de parcelas sem juros
			brand: brand, //Bandeira do cartão
			success: function(installments){
				var parcelas = installments.installments[brand];
				var qntParcelas = '<?php echo $qntParcelas ?>';
				
				$('#parcelas').html('');
				
				$.map(parcelas, function(e){
					if (qntParcelas >= e.quantity) {
						$('#parcelas').append('<option data-value="' + e.quantity + '" value="' + e.installmentAmount + '">' + e.quantity + 'x de ' + formatMoney(e.installmentAmount) + '</option>');
					}
				});
				
				$('.vhide').fadeIn('slow');
			}
		});
	}
	
	function formatMoney(val) {
		var n = val.toString().indexOf('.');
		var str = val.toString();
		
		if (n == '-1') {
			return 'R$' + str + ',00';
		} else {
			return 'R$' + str.replace('.', ',');
		}
	}
	
	$('#button-confirm').click(function() {
		
		$('#warning').html('').hide();
		
		var expiration = $('input#validade').val().split('/');
		
		PagSeguroDirectPayment.createCardToken({
			cardNumber: $('input#numero-cartao').val(),
			brand: $('input#bandeira').val(),
			cvv: $('input#cvv').val(),
			expirationMonth: expiration[0],
			expirationYear: expiration[1],
			success: function(data) {
				$.ajax({
					url: 'index.php?route=payment/pagseguro_cartao/transition',
					data: 'creditCardToken=' + data.card.token + '&senderHash=' + PagSeguroDirectPayment.getSenderHash() + '&installmentQuantity=' + $('select#parcelas option:selected').attr('data-value') + '&installmentValue=' + $('select#parcelas').val() + '&creditCardHolderName=' + $('input#nome').val() + '&creditCardHolderCPF=' + $('input#cpf').val() + '&creditCardHolderBirthDate=' + $('input#data-nascimento').val() + '&creditCardHolderPhone=' + $('input#telefone').val(),
					type: 'POST',
					dataType: 'JSON',
          beforeSend: function() {
            $('#button-confirm').button('loading');
          },
					success: function(data){
						if (data.error) {
							$('#warning').html(data.error.message).show();
						} else {
							$(this).attr('disabled');
							
							$.ajax({
								url: 'index.php?route=payment/pagseguro_cartao/confirm',
								data: 'status=' + data.status,
								type: 'POST',
								success: function() {
									location.href = '<?php echo $continue ?>'
								}
							});
						}
					},
          complete: function() {
            $('#button-confirm').button('reset');
          }
				});
			},
			error: function(data) {
				var html = '<ul>';
				$.map(data.errors, function(e){
					html += '<li>' + e + '</li>';
				});
				html += '</ul>';
				
				$('#warning').html(html).show();
			}
		});
	});

  $('#numero-cartao').bind("paste",function(e) {
    getInstallments();
  });
</script>