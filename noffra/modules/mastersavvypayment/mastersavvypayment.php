<?php

if (!defined('_PS_VERSION_'))
    exit;

include(_PS_MODULE_DIR_ . 'mastersavvypayment/lib/nusoap.php');

define("NUSOAP_HOST", "");
define("NUSOAP_PORT", "");
define("NUSOAP_USERNAME", "");
define("NUSOAP_PASSWORD", "");

class mastersavvypayment extends PaymentModule {

    private $_html = '';
    private $_postErrors = array();

    function __construct() {
        $this->name = 'mastersavvypayment';
        $this->tab = 'payments_gateways';
        $this->version = 4;
        $this->author = '123pago';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6.0', 'max' => '1.7.1.6');
        $this->dependencies = array();

        $this->currencies = true;
        $this->currencies_mode = 'checkbox';

        parent::__construct();

        $this->page = basename(__FILE__, '.php');
        $this->displayName = $this->l('123pago');
        $this->description = $this->l('Recibe pagos con tarjetas de credito usando 123pagos.net un producto de MasterSavvy');
		$this->displayFail =$this->l('Payment closed expired or closed cancelled');

        $this->confirmUninstall = $this->l('Esta seguro que desea desinstalar?');

        if (!Configuration::get('MS_API_KEY'))
            $this->warning = $this->l('Necesitas especificar un api key de 123pago');

        if (!Configuration::get('MS_NB_PROVEEDOR'))
            $this->warning = $this->l('Necesitas especificar un nb proveedor de 123pago');

        if (!Configuration::get('MS_ID_PROVEEDOR'))
            $this->warning = $this->l('Necesitas especificar un id proveedor de 123pago');

        if (!sizeof(Currency::checkPaymentCurrencies($this->id)))
            $this->warning = $this->l('No hay monedas relacionadas para este modulo');
    }

    public function install() {
        if (Shop::isFeatureActive())
            Shop::setContext(Shop::CONTEXT_SHOP);

        if (!parent::install() OR !$this->registerHook('payment') OR !$this->registerHook('paymentReturn') OR !$this->registerHook('invoice') OR !$this->createOrderState())
            return false;

        Configuration::updateValue('MS_WIDTH', '200px');
        Configuration::updateValue('MS_TESTING_MODE', '3');

        return true;
    }

    public function createOrderState() {
        if (!Configuration::get('MASTERSAVVY_OS_AUTHORIZATION')) {
            $orderState = new OrderState();
            $orderState->name = array();

            foreach (Language::getLanguages() as $language) {
                $orderState->name[$language['id_lang']] = 'Pago aceptado en 123pago';
            }

            $orderState->send_email = true;
            $orderState->color = '#005cb8';
            $orderState->hidden = false;
            $orderState->delivery = true;
            $orderState->logable = true;
            $orderState->invoice = true;
            $orderState->template = 'payment';

            if ($orderState->add()) {
                $source = dirname(__FILE__) . 'img/state_ms_1.gif';
                $destination = dirname(__FILE__) . '/../../img/os/' . (int) $orderState->id . '.gif';
                copy($source, $destination);
            }
            Configuration::updateValue('MASTERSAVVY_OS_AUTHORIZATION', (int) $orderState->id);
        }

        if (!Configuration::get('MASTERSAVVY_OS_PENDING')) {
            $orderState = new OrderState();
            $orderState->name = array();

            foreach (Language::getLanguages() as $language) {
                $orderState->name[$language['id_lang']] = 'Pago pendiente en 123pago';
            }

            $orderState->send_email = false;
            $orderState->color = '#DDEEFF';
            $orderState->hidden = false;
            $orderState->delivery = false;
            $orderState->logable = false;
            $orderState->invoice = false;

            if ($orderState->add()) {
                $source = dirname(__FILE__) . 'img/state_ms_2.gif';
                $destination = dirname(__FILE__) . '/../../img/os/' . (int) $orderState->id . '.gif';
                copy($source, $destination);
            }
            Configuration::updateValue('MASTERSAVVY_OS_PENDING', (int) $orderState->id);
        }

        if (!Configuration::get('MASTERSAVVY_OS_REFUSED')) {
            $orderState = new OrderState();
            $orderState->name = array();

            foreach (Language::getLanguages() as $language) {
                $orderState->name[$language['id_lang']] = 'Pago rechazado en 123pago';
            }

            $orderState->send_email = false;
            $orderState->color = '#ffaa1c';
            $orderState->hidden = false;
            $orderState->delivery = false;
            $orderState->logable = false;
            $orderState->invoice = false;

            if ($orderState->add()) {
                $source = dirname(__FILE__) . 'img/state_ms_3.gif';
                $destination = dirname(__FILE__) . '/../../img/os/' . (int) $orderState->id . '.gif';
                copy($source, $destination);
            }
            Configuration::updateValue('MASTERSAVVY_OS_REFUSED', (int) $orderState->id);
        }
        return true;
    }

    public function uninstall() {
        if (!parent::uninstall())
            return false;

        Configuration::deleteByName('MASTERSAVVY_OS_AUTHORIZATION');
        Configuration::deleteByName('MASTERSAVVY_OS_PENDING');
        Configuration::deleteByName('MASTERSAVVY_OS_REFUSED');

        return true;
    }

    private function callPaymentButton($post_values) {

        if (Configuration::get('MS_TESTING_MODE') == 1) {
            $post_url = "https://totalpos.123pago.net/msBotonDePago/index.jsp";
        } else if (Configuration::get('MS_TESTING_MODE') == 2) {
            $post_url = "https://sandbox.123pago.net/msBotonDePago/index.jsp";
        } 

        // esta sección toma los valores de entrada requeridos por 123Pago y los
        // convierte en el formato correspondiente en el protocolo http post.
        $post_string = "";
        foreach ($post_values as $key => $value) {
            $post_string .= "$key=" . urlencode($value) . "&";
        }
        $post_string = rtrim($post_string, "& ");

        $request = curl_init($post_url); // instancia el objeto curl
        curl_setopt($request, CURLOPT_VERBOSE, 1);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1); // retorna data de respuesta TRUE(1)
        curl_setopt($request, CURLOPT_POSTFIELDS, $post_string); // usa HTTP POST para enviar data de la forma.
        curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE); // des comente esta línea si no quiere obtener
        curl_setopt($request, CURLOPT_STDERR, fopen(dirname(__FILE__) . '/log/mastersavvy.log', "w+"));

        //respuesta de gateway
        $post_response = curl_exec($request); // ejecuta el curl post y almacena el resultado en in $post_response
        // es posible que se requiera el uso de opciones adicionales a las indicada dependiendo de la
        //configuración de su servidor
        // puede encontrar documentación de las opciones de curl en http://www.php.net/curl_setopt

        curl_close($request); // cierra el objeto curl										

        /*
          $post_response = str_replace("width:220px", "width:150px; text-align: right;", $post_response);
          $post_response = str_replace("padding:10px;", "", $post_response);
          $post_response = str_replace("color:gray;", "", $post_response);
          $post_response = str_replace("_blank", "_self", $post_response);
          $post_response = str_replace("90%", "70%", $post_response);
         */

        return $post_response;
    }

    function hookPayment($params) {

        if (!$this->active)
            return;

        if (!$this->checkCurrency($params['cart']))
            return;

        global $smarty, $cookie;
        
        // Realizar validacion antes de llamar al boton de pagos para verificar si
        // el carrito ya fué pagado.
        
        $cart = Context::getContext()->cart;

        $smarty->caching = false;
        $smarty->force_compile = true;

        $cart_details = $cart->getSummaryDetails(null, true);
        $billing_address = new Address($cart->id_address_invoice);
        $proveedor    = new customer($cart->id_customer);
        $dni          = $proveedor->dni_ci;

      //  $dni = $billing_address->dni;



        
        $phone = $billing_address->phone_mobile;

        $post_values = array(
            "nbproveedor" => Configuration::get('MS_NB_PROVEEDOR'),
            "nb" => Tools::safeOutput(($cookie->logged ? $cookie->customer_firstname : '')),
            "ap" => Tools::safeOutput(($cookie->logged ? $cookie->customer_lastname : '')),
            "ci" => $dni,
            "em" => Tools::safeOutput(($cookie->logged ? $cookie->email : '')),
            "cs" => Configuration::get('MS_API_KEY'),
            "co" => Configuration::get('PS_SHOP_NAME') . ', pedido #' . $cart->id,
            "tl" => $phone,
            "mt" => $cart_details['total_price'],
            "nai" => $cart->id,
            "ip" => $_SERVER["REMOTE_ADDR"],
            // "ancho" => Configuration::get('MS_WIDTH'),
            "ancho" => "220px"
        );

        $cookie->nai = $cart->id;
        $cookie->write();

        $post_response = $this->callPaymentButton($post_values);

        $smarty->assign(array(
            'MS_RENDERED_BUTTON' => $post_response,
            'TESTING_MODE' => (in_array(Configuration::get('MS_TESTING_MODE'), array(2, 3)) ? 'OK' : ''),
            'this_path' => $this->_path,
            'this_path_ssl' => Configuration::get('PS_FO_PROTOCOL') . $_SERVER['HTTP_HOST'] . __PS_BASE_URI__ . "modules/{$this->name}/",
        ));

        return $this->display(__FILE__, 'payment.tpl');
    }

    public function hookPaymentReturn($params) {
        if (!$this->active)
            return;

        $state = $params['objOrder']->getCurrentState();
        if ($state == Configuration::get('MASTERSAVVY_OS_AUTHORIZATION')) {
            $this->smarty->assign(array(
                'total_to_pay' => Tools::displayPrice($params['total_to_pay'], $params['currencyObj'], false),
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

    public function getContent() {
        $output = null;


        if (Tools::isSubmit('submit' . $this->name)) {
            $nb_proveedor = strval(Tools::getValue('MS_NB_PROVEEDOR'));
            if (!$nb_proveedor || empty($nb_proveedor))
                $output .= $this->displayError($this->l('Parametro Invalido'));
            else {
                Configuration::updateValue('MS_NB_PROVEEDOR', $nb_proveedor);
                $output .= $this->displayConfirmation($this->l('El NB PROVEEDOR ha sido actualizado'));
            }

            $id_proveedor = strval(Tools::getValue('MS_ID_PROVEEDOR'));
            if (!$id_proveedor || empty($id_proveedor))
                $output .= $this->displayError($this->l('Parametro Invalido'));
            else {
                Configuration::updateValue('MS_ID_PROVEEDOR', $id_proveedor);
                $output .= $this->displayConfirmation($this->l('El ID PROVEEDOR ha sido actualizado'));
            }


            $api_key = strval(Tools::getValue('MS_API_KEY'));
            if (!$api_key || empty($api_key))
                $output .= $this->displayError($this->l('Parametro Invalido'));
            else {
                Configuration::updateValue('MS_API_KEY', $api_key);
                $output .= $this->displayConfirmation($this->l('El API KEY ha sido actualizado'));
            }

            $testing_mode = Tools::getValue('MS_TESTING_MODE');
            if (!$testing_mode || empty($testing_mode))
                $output .= $this->displayError($this->l('Parametro Invalido'));
            else {
                Configuration::updateValue('MS_TESTING_MODE', $testing_mode);
                $output .= $this->displayConfirmation($this->l('El TESTING_MODE ha sido actualizado'));
            }

            Configuration::updateValue('MS_WIDTH', '150');
        }
        return $output . $this->displayForm();
    }

    public function displayForm() {
        // Get default Language
        $default_lang = (int) Configuration::get('PS_LANG_DEFAULT');

        // Init Fields form array
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Settings'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('API_KEY'),
                    'name' => 'MS_API_KEY',
                    'size' => 20,
                    'required' => true
                ),
                array(
                    'type' => 'radio',
                    'label' => $this->l('TESTING_MODE'),
                    'name' => 'MS_TESTING_MODE',
                    'class' => 't',
                    'values' => array(
                        array(
                            'id' => 'test',
                            'value' => 2,
                            'label' => $this->l('Modo de Pruebas')
                        ),
                        array(
                            'id' => 'prod',
                            'value' => 1,
                            'label' => $this->l('Modo de Producción')
                        )
                    ),
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('NB_PROVEEDOR'),
                    'name' => 'MS_NB_PROVEEDOR',
                    'size' => 20,
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('ID_PROVEEDOR'),
                    'name' => 'MS_ID_PROVEEDOR',
                    'size' => 20,
                    'required' => true,
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'button'
            )
        );

        $helper = new HelperForm();

        // Module, t    oken and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        // Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit' . $this->name;
        $helper->toolbar_btn = array(
            'save' =>
            array(
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name .
                '&token=' . Tools::getAdminTokenLite('AdminModules'),
            ),
            'back' => array(
                'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            )
        );

        // Load current value
        $helper->fields_value['MS_NB_PROVEEDOR'] = Configuration::get('MS_NB_PROVEEDOR');
        $helper->fields_value['MS_TESTING_MODE'] = Configuration::get('MS_TESTING_MODE');
        $helper->fields_value['MS_API_KEY'] = Configuration::get('MS_API_KEY');
        $helper->fields_value['MS_ID_PROVEEDOR'] = Configuration::get('MS_ID_PROVEEDOR');

        return $helper->generateForm($fields_form);
    }

    public function checkCurrency($cart) {
        $currency_order = new Currency($cart->id_currency);
        $currencies_module = $this->getCurrency($cart->id_currency);
        $currency_default = Configuration::get('PS_CURRENCY_DEFAULT');
        if (is_array($currencies_module))
            foreach ($currencies_module as $currency_module)
                if ($currency_order->id == $currency_module['id_currency'])
                    return true;
        return false;
    }

    public function validate123pago($nai) {
        
        if (Configuration::get('MS_TESTING_MODE') == 1) {
            // Produccion
            $mastersavvy = new nusoap_client('https://totalpos.123pago.net/ms_comprobar/comprobarPedido?WSDL', 'wsdl', NUSOAP_HOST, NUSOAP_PORT, NUSOAP_USERNAME, NUSOAP_PASSWORD);
        } else if (Configuration::get('MS_TESTING_MODE') == 2) {
            // Pre Produccion
            $mastersavvy = new nusoap_client('http://190.153.48.117/ms_comprobar/comprobarPedido?WSDL', 'wsdl', NUSOAP_HOST, NUSOAP_PORT, NUSOAP_USERNAME, NUSOAP_PASSWORD);
        } 

        $err = $mastersavvy->getError();

        if ($err) {
            return '<h2>Constructor error</h2><pre>' . $err . '</pre>';
        }
        
        $idpv = Configuration::get('MS_ID_PROVEEDOR');
        
        $xml = "<S:Envelope xmlns:S=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:SOAP-ENV=\"http://schemas.xmlsoap.org/soap/envelope/\">" ;
        $xml .= "<SOAP-ENV:Header/>";
        $xml .= "<S:Body>";
        $xml .= "<ns2:verificarPedido xmlns:ns2=\"http://service.ms/\">";
        $xml .= "<orden>$nai</orden>";
        $xml .= "<proveedor>$idpv</proveedor>";
        $xml .= "</ns2:verificarPedido>";
        $xml .= "</S:Body>";
        $xml .= "</S:Envelope>";
        $endpoint = '';
        
        $xmlparser = xml_parser_create();
        $response = @$mastersavvy->send($xml, $endpoint);
        $result = json_decode(json_encode(simplexml_load_string($response["return"])),TRUE);
        if ($result["deuda"] == "NO_PAGADA") {
            return false;
        } else if ($result["deuda"] == "PAGADA"){
            return true;
        }
        return false;
    }
}
?>
