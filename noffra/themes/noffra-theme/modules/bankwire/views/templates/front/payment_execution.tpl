{*
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{capture name=path}
    <a href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html':'UTF-8'}" title="{l s='Go back to the Checkout' mod='bankwire'}">{l s='Checkout' mod='bankwire'}</a><span class="navigation-pipe">{$navigationPipe}</span>{l s='Bank-wire payment' mod='bankwire'}
{/capture}

<h1 class="page-heading">
    {l s='Order summary' mod='bankwire'}
</h1>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

{if $nbProducts <= 0}
    <p class="alert alert-warning">
        {l s='Your shopping cart is empty.' mod='bankwire'}
    </p>
{else}
    <form action="{$link->getModuleLink('bankwire', 'validation', [], true)|escape:'html':'UTF-8'}" method="post" class="form-inline" id="bankForm">
        <div class="box cheque-box">
            <h3 class="page-subheading">
                {l s='Bank-wire payment' mod='bankwire'}
            </h3>
            <p class="cheque-indent alert alert-warning col-xs-12 text-left">
                    {l s='You have chosen to pay by bank wire.' mod='bankwire'}<br/>
                    {l s='Here is a short summary of your order:' mod='bankwire'}
            </p>
            <div class="row">
                <div class="col-md-7">
                    <table class="table-condensed">
                        <tr>
                            <td>{l s='Name of account owner' mod='bankwire'}</td>
                            <td>{$bankData.wireOwner}</td>
                        </tr>
                        <tr>
                            <td>{l s='Include these details' mod='bankwire'}</td>
                            <td>{$bankData.wireDetails}</td>
                        </tr>
                        <tr>
                            <td>{l s='Bank name' mod='bankwire'} </td>
                            <td>{$bankData.wireAddress}</td>
                        </tr>
                        <tr>
                            {if $currencies|@count > 1}
                            <td>{l s='We allow several currencies to be sent via bank wire.' mod='bankwire'}</td>
                            <td>
                                <div class="form-group">
                                    <label>{l s='Choose one of the following:' mod='bankwire'}</label>
                                    <select id="currency_payment" class="form-control" name="currency_payment">
                                        {foreach from=$currencies item=currency}
                                            <option value="{$currency.id_currency}" {if $currency.id_currency == $cust_currency}selected="selected"{/if}>
                                                {$currency.name}
                                            </option>
                                        {/foreach}
                                    </select>
                                </div>
                            </td>
                        {else}
                            <td>{l s='We allow the following currency to be sent via bank wire:' mod='bankwire'}</td>
                            <td>{$currencies.0.name}
                            <input type="hidden" name="currency_payment" value="{$currencies.0.id_currency}" />
                            </td>
                        {/if}
                        </tr>
                    </table>
                </div>
                <div id="payment-prepaid" class="col-md-5">
                    <div class="well">
                        <!-- VARIABLES PARA MOSTRAR LOS PRECIOS CON DESCUENTO -->
                        {*assign var=discount_rate value=0.05*}
                        {assign var=discount_rate value=0}
                        <!-- TABLA CON DESCUENTO -->
                        <table class="table table-condensed" id="con-descuento">
                            <tbody>
                                <tr>
                                    <td>{l s='Total (tax excl.)' mod='bankwire'}</td>
                                    <td class="text-right">{displayPrice price=$cartTotal.total_price_without_tax}</td>
                                </tr>
                                <!--tr class="info">
                                    <td class="info">{l s='5% discount' mod='bankwire'}</td>
                                    <td class="info text-right">{displayPrice price=$cartTotal.total_price_without_tax*$discount_rate*-1}</td>
                                </tr-->
                                <tr>
                                    <td>{l s='Tax' mod='bankwire'}</td>
                                    <td class="text-right">{displayPrice price=$cartTotal.total_tax-($cartTotal.total_tax*$discount_rate)}</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="active">
                                    <th>{l s='Total' mod='bankwire'}</th>
                                    <th class="text-right">{displayPrice price=$cartTotal.total_price-($cartTotal.total_price*$discount_rate)}</th>
                                </tr>
                            </tfoot>
                        </table>
                        <!-- TABLA SIN DESCUENTO -->
                        <table class="table table-condensed hidden" id="sin-descuento">
                            <tbody>
                                <tr>
                                    <td>{l s='Total (tax excl.)' mod='bankwire'}</td>
                                    <td class="text-right">{displayPrice price=$cartTotal.total_price_without_tax}</td>
                                </tr>
                                <tr>
                                    <td>{l s='Tax' mod='bankwire'}</td>
                                    <td class="text-right">{displayPrice price=$cartTotal.total_tax}</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="active">
                                    <th>{l s='Total' mod='bankwire'}</th>
                                    <th class="text-right">{displayPrice price=$cartTotal.total_price}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div> <!-- .row -->
            <div class="row">
                <div class="col-xs-12">
                    <div class="well">
                        <!-- CONTRIBUYENTE ESPECIAL -->
                        <!--<label class="checkbox">
                            <input type="checkbox" class="form-control " name="contribuyente" id="contribuyente" value="contribuyente" class="nothanks">{l s='Soy contribuyente especial' mod='bankwire'}
                        </label><br/>-->
                        <!-- NO DESCUENTO -->
                        <label class="checkbox">
                            <input type="checkbox" class="form-control " name="nothanks" id="nothanks" value="nothanks" class="nothanks">{l s='No deseo el descuento adicional' mod='bankwire'}
                        </label>
                    </div>
                </div>
                <div class="col-xs-12">
                    <table id="transacciones" class="table table-hover table-responsive">
                        <thead>
                            <tr>
                                <th></th>
                                <th class="text-center">{l s='Bank' mod='bankwire'}</th>
                                <th class="text-center">{l s='Reference number' mod='bankwire'}</th>
                                <th class="text-center">{l s='Amount' mod='bankwire'}</th>
                            </tr>
                        </thead>
                        <tbody>
                        <tr id="0">
                            <td class="text-center">
                                
                            </td>
                            <td class="text-center">
                                <select name="bank[]" style='width:100%' id="bank0" disabled>
                                    <option value="" disabled selected>Banco</option>
                                </select>
                            </td>
                            <td class="text-center">
                                <input type="text" name="reference[]" id="reference0" class="reference_input form-control" disabled maxlength="16">
                            </td>
                            <td class="text-center">
                                <input type="text" name="amount[]" id="amount0" class="amount_input form-control" disabled>
                            </td>
                        </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4">
                                    <button type="button" name="addButton" id="addButton" class="btn btn-sm btn-success" disabled>Agregar </button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <p class="label label-alert">
                    {l s='Please confirm your order by clicking "I confirm my order".' mod='bankwire'}
                </p>
            </div><!-- .row -->
        </div><!-- .cheque-box -->
        <p class="cart_navigation clearfix" id="cart_navigation">
            <a class="button-exclusive btn btn-default" href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html':'UTF-8'}">
                <i class="icon-chevron-left"></i>{l s='Other payment methods' mod='bankwire'}
            </a>
            <button class="button btn btn-default button-medium" type="submit" id="bankButton" disabled>
                <span>{l s='I confirm my order' mod='bankwire'}<i class="icon-chevron-right right"></i></span>
            </button>
        </p>
    </form>

	<script>
        $(document).ready(function() {
            var i = 0;
            var selectbox = null;
            var msgError = null;

            //Cargar la lista de los bancos
            $.ajax({
                type: 'POST',
                url: '{$base_dir_ssl}apps/bank_references/controllers/bank_controller.php'
            })
            .done(function(listaBancos) {
                selectbox = listaBancos;
                $("#bank0").html(listaBancos).attr("disabled", false);
            })
            .fail(function() {
                alert("Hubo un error al cargar la lista de los bancos");
            });

            // Checkbox para contribuyente especial - FUNCION ORIGINAL
            $("#contribuyente").on("change", function () {
                if ( $(this).is(":checked") ) {
                    $("#transacciones").css("display","none");
                    $("#bankButton").prop("disabled", false);
                    $("#nothanks").prop("checked",false);
                    $("#uniform-nothanks span").removeClass();
                } else {
                    $("#transacciones").css("display","table");
                    $("#bankButton").prop("disabled", true);
                }
            });

            //Checkbox para cliente que no quiere descuento - FUNCION EDITADA 
            $("#nothanks").change(function () {
                if (this.checked) {
                    $("#transacciones").css("display","none");
                    $("#bankButton").prop("disabled", false);
                    $("#contribuyente").prop("checked",false);
                    $("#uniform-contribuyente span").removeClass();
                    $("#con-descuento").addClass("hidden");
                    $("#sin-descuento").removeClass("hidden");
                } else {
                    $("#transacciones").css("display","table");
                    $("#bankButton").prop("disabled", true);
                    $("#con-descuento").removeClass("hidden");
                    $("#sin-descuento").addClass("hidden");
                }
            });

            //Agregar nuevo tr al tbody
            $("#addButton").on("click", function () {
                $("#bankButton").attr("disabled", true);
                $("#addButton").attr("disabled", true);
				i++;
				$('#transacciones tbody').append(
				    "<tr id='" + i + "'>" +
						"<td>" +
							"<button type='button' name='removebutton' id='" + i + "' class='btn btn-sm btn-block btn-danger btn_remove'><i class='icon-trash'></i></button>" +
						"</td>" +
						"<td>" +
							"<select name='bank[]' style='width:100%' id='bank" + i + "'>" +
							selectbox +
							"</select>" +
						"</td>" +
						"<td>" +
							"<input type='text' name='reference[]' id='reference" + i + "' class='reference_input form-control' disabled maxlength='16'>" +
						"</td>" +
                    	"<td>" +
                    		"<input type='text' name='amount[]' id='amount" + i + "' class='amount_input form-control' disabled>" +
                    	"</td>" +
					"</tr>").hide().fadeIn(500);
            });

            //Botón para eliminar tr
            $(document).on('click', '.btn_remove', function(){
                var button_id = $(this).attr("id");
                $('#'+button_id+'').remove();
                if (button_id == i) {
                    //ver ALGORITMO #1
                    validateBankReference();
                }
            });

            //Activar reference correspondiente al select escogido
			$(document).on("change", 'select', function () {
				var id = $(this).closest("tr").attr("id");
                $('#reference'+id+'').attr("disabled", false);

                //ver ALGORITMO #1
                //Mostrar mensaje de error si existe un número de transacción duplicado
                msgError = false;
                validateBankReference();
                if (msgError) {
                    alert("Número de transacción duplicado");
                }
            });

            //Activar amount correspondiente al llenar de manera correcta el campo reference
			$(document).on("input", ".reference_input", function () {
                var id = $(this).closest("tr").attr("id");
                var numberReference = $('#reference'+id+'').val();
                numberReference = numberReference.trim();
				var valNumberReference = parseFloat(numberReference);

                if (numberReference.length >= 8 && numberReference.length <= 16 && !isNaN(numberReference) && Number.isInteger(valNumberReference)) {
                    $('#amount'+id+'').attr("disabled", false);
                } else {
                    $('#amount'+id+'').attr("disabled", true).val("");
                }
            });

            //Mostrar mensaje de error si existe un número de transacción duplicado
            $(document).on("blur", ".reference_input", function () {
                //ver ALGORITMO #1
				msgError = false;
                validateBankReference();
                if (msgError) {
                    alert("Número de transacción duplicado");
                }
            });

			//Activar botones al llenar de manera correcta el campo amount
            $(document).on("input", ".amount_input", function () {
                //ver ALGORITMO #1
                validateBankReference();
            });

            //Restringir inputs
            $(document).on("focus", ".reference_input", function () {
                $(this).keypress(function(tecla) {
                    if(tecla.charCode < 48 || tecla.charCode > 57) return false;
                });
            });
            $(document).on("focus", ".amount_input", function () {
                $(this).keypress(function(tecla) {
                    if(tecla.charCode < 46 || tecla.charCode > 57 || tecla.charCode === 47) return false;
                });
            });

            //Redondear número a dos decimales
            $(document).on("blur", ".amount_input", function () {
                var numberAmount = $(this).val();
                if (numberAmount != "") {
                    numberAmount = parseFloat(numberAmount).toFixed(2);
                    $(this).val(numberAmount);
                } else {
                    $(this).val("");
                }
            });

            //Almacenar datos bancarios en la BDD
            $("#bankForm").on("submit", function (e) {
                e.preventDefault();
                var self = this;
                var cartId = {$cartId};

                $.ajax({
                    type: 'POST',
                    url: '{$base_dir_ssl}apps/bank_references/controllers/bank_references_controller.php',
                    data : $('#bankForm').serialize() + "&cartId=" + cartId
                })
                .done(function(result) {
                    if (result === "") self.submit();
                })
                .fail(function() {
                    alert("Ha ocurrido un error en la transacción, intente de nuevo");
                });
            });

            /*
			 * ALGORITMO #1
			 * Activar o desactivar botones dependiendo del sw_error y el estado de los inputs
			 *
			 * El booleano 'sw_error' será 'true' si coinciden dos bancos iguales con el mismo número de referencia.
			 * Si es 'false', se comprueba que los inputs esten bien seteados. En caso de que lo estén, se
			 * activarán los botones AGREGAR ITEM y CONFIRMAR PEDIDO PREPAGADO.
			 */
            function validateBankReference() {
                var sw_error = false;
                var banco, banco1, reference, reference1;
                for (var j = 0; j <= i; j++) {
                    banco = $('#bank'+j+'').val();
                    reference =  $('#reference'+j+'').val();
                    for (var k = 0; k <= i; k++) {
                        if (j != k) {
                            banco1 = $('#bank'+k+'').val();
                            reference1 =  $('#reference'+k+'').val();
                            if (banco == banco1 && reference == reference1 && (banco != undefined || banco1 != undefined)) {
                                sw_error = true;
                            }
                        }
                    }
                }
                if (sw_error) {
                    msgError = true;
                    $("#bankButton").attr("disabled", true);
                    $("#addButton").attr("disabled", true);
                } else {
                    validateInputs();
                }
            }

            //Verificar que los inputs estén bien seteados
            function validateInputs() {
                var numberReference;
                var numberAmount;
                var sw_error = false; //Usado para determinar el estado de todos los campos reference y amount
                for (var j = 0; j <= i; j++) {
                    numberReference = $('#reference'+j+'').val();
                    numberAmount = $('#amount'+j+'').val();
                    /*
					 * -Si el input se eliminó, tendrá un valor "undefined". Este valor será 12345678 con el fin que
					 * no afecte el resto de validaciones.
					 */
                    if (numberReference == undefined) {
                        numberReference = "12345678";
                    }
                    /*
					 * -Si el input es nuevo y vacío, se asignará el valor de cero con el fin de activar sw_error.
					 * -Si el input se eliminó, tendrá un valor "undefined". Este valor será uno con el fin que
					 * no afecte el resto de validaciones.
					 */
                    if (numberAmount == "") {
                        numberAmount = 0;
                    } else if (numberAmount == undefined) {
                        numberAmount = 1;
                    }
                    if (!isNaN(numberAmount)) {
                        numberAmount = parseFloat(numberAmount);
                    }

                    if (numberReference.length < 8 || numberReference.length > 16 || isNaN(numberReference) || numberAmount === 0 || isNaN(numberAmount)) {
                        sw_error = true;
                    }
                }
                if (sw_error) {
                    $("#bankButton").attr("disabled", true);
                    $("#addButton").attr("disabled", true);
                } else {
                    $("#bankButton").attr("disabled", false);
                    $("#addButton").attr("disabled", false);
                }
            }
        });
	</script>
{/if}
