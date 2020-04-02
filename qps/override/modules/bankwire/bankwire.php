<?php
/*
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
*/

if (!defined('_PS_VERSION_'))
	exit;

class BankWireOverride extends BankWire
{

	public function hookPayment($params)
	{
		$products = $this->context->cart->getProducts();

		if($products)
		{
			// si algun producto tiene disponible en stock cero o menos unidades o si la cantidad solicitada de un producto supera 
			// el stock disponible, entonces solo le permite hacer una solicitud de pedido.
			// caso contrario si tiene en stock mas de cero unidades disponible entonces le muestro todas las formas de pago
			// (paypal,traferencia,cheque,pago a credito).
			foreach ($products as $productos) {
				if ($productos['quantity_available'] <= 0 || ($productos['cart_quantity']>$productos['quantity_available']))  
				{
				return;
				}
			}
		}

		if (!$this->active)
			return;
		if (!$this->checkCurrency($params['cart']))
			return;

		$this->smarty->assign(array(
			'this_path' => $this->_path,
			'this_path_bw' => $this->_path,
			'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/'
		));
		return $this->display(__FILE__, 'payment.tpl');
	}

}
