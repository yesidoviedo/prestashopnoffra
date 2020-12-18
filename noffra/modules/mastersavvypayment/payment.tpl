<script type="text/javascript" src="{$base_dir_ssl}modules/mastersavvypayment/js/mastersavvypayment.js"></script>
<div class="row" id="is_123pago" style="margin-bottom: 10px;">
	<div class="col-xs-12">
		<div class="payment_module" style="background-color: #5bc55b; border: 1px solid #dddddd; padding-top: 1%; height: 125px;">
			{$MS_RENDERED_BUTTON}
	    <span id="validationResult"></span>
	    </div>
	</div>
</div>
<!--form action="{$link->getModuleLink('moduloprobas', 'validation', [], true)|escape:'html'}" method="post" style="visibility: hidden;">
    <p class="cart_navigation" id="cart_navigation">
    <input type="submit" id="action-button" value="Confirmar pedido" class="exclusive_large" />
    </p>
</form-->
<form action="{$base_dir_ssl}historial-compra" method="post" style="visibility: hidden;">
    <p class="cart_navigation" id="cart_navigation">
        <input type="submit" id="action-button" value="Confirmar pedido" class="exclusive_large" />
    </p>
</form>
<div style="clear: both;"></div>
<script type="text/javascript">
    $(window).focus(function(){
        validate123pago("{$base_dir_ssl}",{$cart->id});
    });

    function validate123pago() {
    var param = {
    	nai: {$cart->id}, base_dir: "{$base_dir_ssl}"
    };
    $.ajax({
        url: "{$base_dir_ssl}"+"modules/mastersavvypayment/validation.php",
        type: "POST",
        data: param,
        success: function(json_data) {
        	}
    	});
	}
    function callback_button(paymentData){
        var parametros = {
            nai: {$cart->id}, 
            tarjeta: paymentData['t_tarjeta'],
            status: paymentData['status'],
            total: paymentData['t_monto'],
            concepto: paymentData['t_concepto'],
            autorizacion : paymentData['t_autorizacion'],
            path:"{$base_dir_ssl}"+"modules/mastersavvypayment/"
        };
        $.ajax({
            url: "{$base_dir_ssl}"+"modules/mastersavvypayment/insertar.php",
            type: "POST",
            data: parametros,
            success: function(json_data) {
                    if (paymentData['status']!="error"){
                        $('#action-button').click();
                    }
                }
            }); 
    }
</script>
