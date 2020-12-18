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

class procesarpedido extends PaymentModule
{
	private $_html = '';
	private $_postErrors = array();

	public $procesarpedidoName;
	public $address;
	public $extra_mail_vars;

	public function __construct()
	{
		$this->name = 'procesarpedido';
		$this->tab = 'payments_gateways';
		$this->version = '2.7.2';
		$this->author = 'PrestaShop';
		$this->controllers = array('payment', 'validation');
		$this->is_eu_compatible = 1;

		$this->currencies = true;
		$this->currencies_mode = 'checkbox';

		$config = Configuration::getMultiple(array('procesarpedido_NAME', 'procesarpedido_ADDRESS'));
		if (isset($config['procesarpedido_NAME']))
			$this->procesarpedidoName = $config['procesarpedido_NAME'];
		if (isset($config['procesarpedido_ADDRESS']))
			$this->address = $config['procesarpedido_ADDRESS'];

		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('Payments by check');
		$this->description = $this->l('This module allows you to accept payments by check.');
		$this->confirmUninstall = $this->l('Are you sure you want to delete these details?');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.6.99.99');

		if ((!isset($this->procesarpedidoName) || !isset($this->address) || empty($this->procesarpedidoName) || empty($this->address)))
			$this->warning = $this->l('The "Pay to the order of" and "Address" fields must be configured before using this module.');
		if (!count(Currency::checkPaymentCurrencies($this->id)))
			$this->warning = $this->l('No currency has been set for this module.');

		$this->extra_mail_vars = array(
											'{procesarpedido_name}' => Configuration::get('procesarpedido_NAME'),
											'{procesarpedido_address}' => Configuration::get('procesarpedido_ADDRESS'),
											'{procesarpedido_address_html}' => str_replace("\n", '<br />', Configuration::get('procesarpedido_ADDRESS'))
											);
	}

	public function install()
	{
		if (!parent::install() || !$this->registerHook('payment') || ! $this->registerHook('displayPaymentEU') || !$this->registerHook('paymentReturn'))
			return false;
		return true;
	}

	public function uninstall()
	{
		if (!Configuration::deleteByName('procesarpedido_NAME') || !Configuration::deleteByName('procesarpedido_ADDRESS') || !parent::uninstall())
			return false;
		return true;
	}

	private function _postValidation()
	{
		
		if (Tools::isSubmit('btnSubmit'))
		{
			if (!Tools::getValue('procesarpedido_NAME'))
				$this->_postErrors[] = $this->l('The "Pay to the order of" field is required.');
			elseif (!Tools::getValue('procesarpedido_ADDRESS'))
				$this->_postErrors[] = $this->l('The "Address" field is required.');
		}
	}

	private function _postProcess()
	{
		if (Tools::isSubmit('btnSubmit'))
		{
			Configuration::updateValue('procesarpedido_NAME', Tools::getValue('procesarpedido_NAME'));
			Configuration::updateValue('procesarpedido_ADDRESS', Tools::getValue('procesarpedido_ADDRESS'));
		}
		$this->_html .= $this->displayConfirmation($this->l('Settings updated'));
	}

	private function _displayprocesarpedido()
	{
		return $this->display(__FILE__, 'infos.tpl');
	}

	public function getContent()
	{
		$this->_html = '';

		if (Tools::isSubmit('btnSubmit'))
		{
			$this->_postValidation();
			if (!count($this->_postErrors))
				$this->_postProcess();
			else
				foreach ($this->_postErrors as $err)
					$this->_html .= $this->displayError($err);
		}

		$this->_html .= $this->_displayprocesarpedido();
		$this->_html .= $this->renderForm();

		return $this->_html;
	}

	public function hookPayment($params)
	{
		//$cart_details = $cart->getSummaryDetails(null, true);
		//var_dump($cart->getOrderTotal());
		//var_dump($params['cart']->allow_seperated_package);
		$products_cart = $this->context->cart->getProducts();
        $i        = 0;
        $mostrar  = "SI";
		foreach ($products_cart as $key => $value) {
			$procesocobro = Supplier::isprocesocobro($products_cart[$i]['id_supplier']);			
			$i++;
			if ($procesocobro == '1'){
				$mostrar = "NO";
			}
		}
		if ($mostrar == "NO"){
			return;
		}

		if (!$this->active)
			return;
		if (!$this->checkCurrency($params['cart']))
			return;

		$this->smarty->assign(array(
			'this_path' => $this->_path,
			'this_path_procesarpedido' => $this->_path,
			'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/'
		));
		return $this->display(__FILE__, 'payment.tpl');
	}

	public function hookDisplayPaymentEU($params)
	{
		if (!$this->active)
			return;
		if (!$this->checkCurrency($params['cart']))
			return;

		$payment_options = array(
			'cta_text' => $this->l('Pay by Check'),
			'logo' => Media::getMediaPath(_PS_MODULE_DIR_.$this->name.'/cheque.jpg'),
			'action' => $this->context->link->getModuleLink($this->name, 'validation', array(), true)
		);

		return $payment_options;
	}

	public function hookPaymentReturn($params)
	{
		if (!$this->active)
			return;

		$state = $params['objOrder']->getCurrentState();
		if (in_array($state, array(Configuration::get('PS_OS_procesarpedido'), Configuration::get('PS_OS_OUTOFSTOCK'), Configuration::get('PS_OS_OUTOFSTOCK_UNPAID'))))
		{
			$this->smarty->assign(array(
				'total_to_pay' => Tools::displayPrice($params['total_to_pay'], $params['currencyObj'], false),
				'procesarpedidoName' => $this->procesarpedidoName,
				'procesarpedidoAddress' => Tools::nl2br($this->address),
				'status' => 'ok',
				'id_order' => $params['objOrder']->id
			));
			if (isset($params['objOrder']->reference) && !empty($params['objOrder']->reference))
				$this->smarty->assign('reference', $params['objOrder']->reference);
		}
		else
			$this->smarty->assign('status', 'failed');
		return $this->display(__FILE__, 'payment_return.tpl');
	}

	public function checkCurrency($cart)
	{
		$currency_order = new Currency((int)($cart->id_currency));
		$currencies_module = $this->getCurrency((int)$cart->id_currency);

		if (is_array($currencies_module))
			foreach ($currencies_module as $currency_module)
				if ($currency_order->id == $currency_module['id_currency'])
					return true;
		return false;
	}

	public function renderForm()
	{
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Contact details'),
					'icon' => 'icon-envelope'
				),
				'input' => array(
					array(
						'type' => 'text',
						'label' => $this->l('Pay to the order of (name)'),
						'name' => 'procesarpedido_NAME',
						'required' => true
					),
					array(
						'type' => 'textarea',
						'label' => $this->l('Address'),
						'desc' => $this->l('Address where the check should be sent to.'),
						'name' => 'procesarpedido_ADDRESS',
						'required' => true
					),
				),
				'submit' => array(
					'title' => $this->l('Save'),
				)
			),
		);

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();
		$helper->id = (int)Tools::getValue('id_carrier');
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'btnSubmit';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);

		return $helper->generateForm(array($fields_form));
	}

	public function getConfigFieldsValues()
	{
		return array(
			'procesarpedido_NAME' => Tools::getValue('procesarpedido_NAME', Configuration::get('procesarpedido_NAME')),
			'procesarpedido_ADDRESS' => Tools::getValue('procesarpedido_ADDRESS', Configuration::get('procesarpedido_ADDRESS')),
		);
	}
}