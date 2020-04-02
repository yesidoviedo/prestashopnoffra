<?php

class AdminAddressesController extends AdminAddressesControllerCore
{

  public function __construct()
    {
        $this->bootstrap = true;
        $this->required_database = true;
        $this->required_fields = array('company','address2', 'postcode', 'other', 'phone', 'phone_mobile', 'vat_number', 'dni');
        $this->table = 'address';
        $this->className = 'Address';
        $this->lang = false;
        $this->addressType = 'customer';
        $this->explicitSelect = true;
        $this->context = Context::getContext();

        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash'
            )
        );

        $this->allow_export = true;

        if (!Tools::getValue('realedit')) {
            $this->deleted = true;
        }

        $countries = Country::getCountries($this->context->language->id);
        foreach ($countries as $country) {
            $this->countries_array[$country['id_country']] = $country['name'];
        }

        $this->fields_list = array(
            'id_address' => array('title' => $this->l('ID'), 'align' => 'center', 'class' => 'fixed-width-xs'),
            'firstname' => array('title' => $this->l('First Name'), 'filter_key' => 'a!firstname'),
            'lastname' => array('title' => $this->l('Last Name'), 'filter_key' => 'a!lastname'),
            'address1' => array('title' => $this->l('Address')),
            'postcode' => array('title' => $this->l('Zip/Postal Code'), 'align' => 'right'),
            'city' => array('title' => $this->l('City')),
            'country' => array('title' => $this->l('Country'), 'type' => 'select', 'list' => $this->countries_array, 'filter_key' => 'cl!id_country'));

        AdminController::__construct();

        $this->_select = 'cl.`name` as country ';
        $this->_join = '
			LEFT JOIN `'._DB_PREFIX_.'country_lang` cl ON (cl.`id_country` = a.`id_country` AND cl.`id_lang` = '.(int)$this->context->language->id.')
			LEFT JOIN `'._DB_PREFIX_.'customer` c ON a.id_customer = c.id_customer
		';
        $this->_where = 'AND a.id_customer != 0 '.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER, 'c');

        if(Context::getContext()->employee->id_profile==4){ //si el usuario es vendedor
           $this->_where .= " AND c.id_employee=".$this->context->employee->id ;
        }

        $this->_use_found_rows = false;
    }

}
?>