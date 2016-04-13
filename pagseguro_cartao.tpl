<style type="text/css">
  @import url('catalog/view/theme/default/stylesheet/pagseguro.cartao.css');
</style>
<div class="content">
  <div class="row-fluid">
    <div class="alert alert-danger vlhide" id="warning" role="alert"></div>
    
    <?php if (strlen($session_id) != 32) { ?>
    <div class="alert alert-danger" id="warning" role="alert"><?php echo $session_id ?></div>
    <?php exit(); ?>
    <?php } ?>

    <div class="form-horizontal">
      <div class="form-group">
        <div id="bandeiras" class="col-sm-7 col-sm-offset-2"></div>
      </div>

      <div id="form" class="col-sm-offset-1 col-sm-6">
        <div class="form-group col-sm-12">
          <label class="col-sm-4 control-label" for="nome">Nome:</label>
          <div class="col-sm-8">
            <input class="form-control" type="text" id="nome" name="nome" placeholder="Ex: Valdeir Santana" />
            <input type="hidden" id="bandeira" name="bandeira" />
          </div>
        </div>
        
        <div class="form-group col-sm-12">
          <label class="col-sm-4 control-label" for="numero-cartao">Número do Cartão:</label>
          <div class="col-sm-8">
            <input class="form-control" type="text" id="numero-cartao" name="numero-cartao" />
          </div>
        </div>
        
        <div class="form-group col-sm-12">
          <label class="col-sm-4 control-label" for="validade">Validade:</label>
          <div class="col-sm-8">
            <input class="form-control" type="text" id="validade" name="validade" placeholder="Ex: 12/2015" />
          </div>
        </div>

        <div class="form-group col-sm-12">
          <label class="col-sm-4 control-label" for="cvv">Código de Segurança:</label>
          <div class="col-sm-8">
            <input class="form-control" type="text" id="cvv" name="cvv" placeholder="Ex: 123 ou 1234" />
          </div>
        </div>
        
        <div class="form-group col-sm-12">
          <div class="col-sm-5 col-sm-offset-4" id="check">
            <div class="checkbox">
              <input type="checkbox" name="check-titular" id="check-titular" <?php echo (!$data_nascimento || !$cpf) ? '' : 'checked' ?> />
              <label for="check-titular">
                Eu sou o títular do cartão
              </label>
            </div>
          </div>
        </div>

        <div class="form-group col-sm-12 titular">
          <label class="col-sm-4 control-label" for="data-nascimento">Data de Nascimento:</label>
          <div class="col-sm-8">
            <input class="form-control" type="text" id="data-nascimento" name="data-nascimento" placeholder="Ex: 03/07/1808" value="<?php echo $data_nascimento ?>" />
          </div>
        </div>

        <div class="form-group col-sm-12 titular">
          <label class="col-sm-4 control-label" for="cpf">CPF:</label>
          <div class="col-sm-8">
            <input class="form-control" type="text" id="cpf" name="cpf" placeholder="Ex: 222.222.222-22" value="<?php echo $cpf ?>" />
          </div>
        </div>

        <div class="form-group col-sm-12 titular">
          <label class="col-sm-4 control-label" for="telefone">Telefone:</label>
          <div class="col-sm-8">
            <input class="form-control" type="text" id="telefone" name="telefone" placeholder="Ex: (11) 9 8765-4321" value="<?php echo $telefone ?>" />
          </div>
        </div>
        
        <div class="alert alert-info alert-info-installments fade col-sm-12">Carregando...</div>
        
        <div class="form-group col-sm-12 vhide">
          <label class="col-sm-4 control-label" for="parcelas">Parcelas:</label>
          <div class="col-sm-8">
            <select class="form-control" id="parcelas" name="parcelas"></select>
          </div>
        </div>
        
        <div class="form-group col-sm-12 vhide">
          <div class="col-sm-5 col-sm-offset-4">
            <button type="button" id="button-confirm" class="btn btn-primary" data-loading-text="Aguarde...">
              <i class="fa fa-credit-card-alt"></i> 
              Pagar
            </button>
          </div>
        </div>
      </div>
      
      <div class="flip-container col-sm-5">
        <div class="flipper">
          <div class="front">
            <div id="credit-card-example-number" style="border: 3px solid #F00;position: absolute;height: 27px;width: 207px;top: 90px;left: 20px;opacity: 0;z-index:1"></div>
            <div id="credit-card-example-validate" style="border: 3px solid #F00;position: absolute;height: 32px;width: 67px;top: 111px;left: 122px;opacity: 0"></div>
            <div id="credit-card-example-customer" style="border: 3px solid #F00;position: absolute;height: 27px;width: 130px;top: 141px;left: 20px;opacity: 0"></div>
            <div id="credit-card-example-logo" style="position: absolute;height: 43px;width: 63px;top: 114px;left: 208px;border-radius: 8px;opacity: 0"></div>
            <img src="catalog/view/theme/default/image/CreditCardFront.gif" style="height:180px" />
          </div>
          <div class="back">
            <div id="credit-card-example-ccv" style="border: 3px solid #F00;position: absolute;height: 37px;width: 50px;top: 60px;left: 225px;opacity: 0"></div>
            <img src="catalog/view/theme/default/image/CreditCardBack.gif" style="height:180px" />
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  if (typeof(PagSeguroDirectPayment) == 'undefined') {
    alert('Erro ao carregar javascript.\nAcesse http://www.valdeirsantana.com.br / Procure pelo módulo / Clique na aba FAQ para obter mais informações.');
  }

	PagSeguroDirectPayment.setSessionId('<?php echo $session_id ?>');
	
	PagSeguroDirectPayment.getPaymentMethods({
		success: function(bandeiras){
			var cards = bandeiras.paymentMethods.CREDIT_CARD.options;
			
			$.map(cards, function(e){
				$('#bandeiras').append('<a class="pull-left" id="' + e.name + '"><div class="overlay"></div><img src="https://stc.pagseguro.uol.com.br' + e.images.MEDIUM.path + '" /></a>');
			});
			
			getBrand();
		},
    error: function(error) {
      $.each(error['errors'], function(index, message){
				alert(message);
			});
    }
	});
	
	var getBrand = function(){
		$('#numero-cartao').change(function(){
			selecionaBandeira();
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
  
  var selecionaBandeira = function() {
    if ($('#numero-cartao').val().length >= 6) {
      $('.alert-info-installments').addClass('in');
      PagSeguroDirectPayment.getBrand({
        cardBin: $('#numero-cartao').val().replace(/\s+/g, ''),
        success: function(card){
          $('#bandeiras').find('.overlay').css('opacity', '0.7');
          $('#bandeiras #' + card.brand.name.toUpperCase()).find('.overlay').css('opacity', 0);
          $('#bandeira').val(card.brand.name);
          $('.alert-info-installments').addClass('out').remove();
          getInstallments(card.brand.name);
          
          var bandeira = $('#bandeiras #' + card.brand.name.toUpperCase()).find('img').attr('src');
          $('.flip-container .front #credit-card-example-logo').css({
            background: '#FFF url(' + bandeira + ') center 8px no-repeat',
            position: 'absolute',
            height: 43,
            width: 63,
            top: 114,
            left: 208,
            borderRadius: 8,
            opacity: 0
          });
          
          $('.flip-container .front #credit-card-example-logo').stop().animate({
            opacity: 1
          }, 1500);
        },
        error: function(error) {
          $.each(error['errors'], function(index, message){
            alert(message);
          });
        }
      });
    }
  }
	
  function formatMoney(val) {
    var valor = val.toString();

    if (valor.match(/\./)) {
      if (valor.match(/(\.[\d]{1})$/)) {
        return "R$ " + valor.replace('.', ',') + "0";
      }
      else if (valor.match(/(\.[\d]{2})$/)) {
        return "R$ " + valor.replace('.', ',');
      }
    }
    else {
      return "R$ " + valor.replace('.', ',') + ",00";
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
							$('#button-confirm').attr('disabled');
							
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
    selecionaBandeira();
    getInstallments();
  });
  
  $('#check-titular').change(function(){
    if ($(this).is(':checked')) {
      $('.titular').slideUp('show');
    } else {
      $('.titular').slideDown('show');
    }
  });
  
  $('#cvv').focus(function(){
    $('.flip-container').toggleClass('flip-container-hover');
  });
  
  $('input:not(#cvv)').focus(function(){
    $('.flip-container').removeClass('flip-container-hover');
  });
  
  $('#cvv').blur(function(){
    $('.flip-container').removeClass('flip-container-hover');
  });
  
  $('#numero-cartao').focus(function(){
    $('#credit-card-example-number').stop().animate({
      opacity:1
    }, 1000);
  });
  
  $('#nome').focus(function(){
    $('#credit-card-example-customer').stop().animate({
      opacity:1
    }, 1000);
  });
  
  $('#validade').focus(function(){
    $('#credit-card-example-validate').stop().animate({
      opacity:1
    }, 1000);
  });
  
  $('#cvv').focus(function(){
    $('#credit-card-example-ccv').stop().animate({
      opacity:1
    }, 1500);
  });
  
  $('input').blur(function(){
    $('.flip-container .front div:not(#credit-card-example-logo), .flip-container .back div').stop().animate({
      opacity:0
    }, 1000);
  });
  
  $('#check-titular').trigger('change');
</script>