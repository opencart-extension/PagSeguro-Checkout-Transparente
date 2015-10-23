<div id="warning" class="alert alert-danger" role="alert" style="display:none"></div>

<?php if (strlen($session_id) != 32) { ?>
  <div class="alert alert-danger" id="warning" role="alert"><?php echo $session_id ?></div>
<?php exit(); } ?>

<div id="info" class="alert alert-info" role="alert" style="display:none">Aguarde....</div>

<div class="form-horizontal col-sm-offset-3">
  <div class="form-group">
    <label class="col-sm-2 control-label">CPF</label>
    <div class="col-sm-10">
      <input type="text" name="cpf" id="cpf" />
    </div>
  </div>
  
  <div class="form-group">
    <div class="col-sm-10 col-sm-offset-2">
      <button type="button" id="button-confirm" class="btn btn-primary">Pagar</button>
    </div>
  </div>
</div>

<script type="text/javascript" src="catalog/view/javascript/pagseguro/colorbox/jquery.colorbox-min.js"></script>
<link href="catalog/view/javascript/pagseguro/colorbox/colorbox.css" rel="stylesheet" media="all" />

<script type="text/javascript">

if (typeof(PagSeguroDirectPayment) == 'undefined') {
  alert('Erro ao carregar JavaScript do PagSeguro.');
}

PagSeguroDirectPayment.setSessionId('<?php echo $session_id ?>');

$(function(){	
	$('#button-confirm').click(function(){
		
		$('#warning').html('').hide();
		
		$.ajax({
			url: 'index.php?route=payment/pagseguro_boleto/transition',
			type: 'POST',
			data: 'senderHash=' + PagSeguroDirectPayment.getSenderHash() + '&cpf=' + $('input#cpf').val(),
			dataType: 'JSON',
      beforeSend: function() {
        $('#button-confirm').button('loading');
      },
			success: function(json){
				if (json.error){
					$('#warning').html(json.error.message).show();
				} else {
					$.colorbox({
						iframe:true,
						open:true,
						href:json.paymentLink,
						innerWidth:'90%',
						innerHeight:'90%',
						onClosed: function () {
							
							$('#info').show();
							
							$.ajax({
								url: 'index.php?route=payment/pagseguro_boleto/confirm',
								data: 'status=' + json.status,
								type: 'POST',
								success: function (){
									setTimeout(function(){
										location.href = '<?php echo $continue ?>';
									}, 5000);
								}
							})
						}
					});
				}
			},
      complete: function(){
        $('#button-confirm').button('reset');
      }
		});
	});
});
</script>