<?php
class Order extends OrderCore
{

    public $id_supplier;
    public $address_ip;
    public $user_agent;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'orders',
        'primary' => 'id_order',
        'fields' => array(
            'id_address_delivery' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_address_invoice' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_cart' =>                    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_currency' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_shop_group' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_shop' =>                    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_lang' =>                    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_customer' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_carrier' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'current_state' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_supplier' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'secure_key' =>                array('type' => self::TYPE_STRING, 'validate' => 'isMd5'),
            'payment' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
            'module' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isModuleName', 'required' => true),
            'recyclable' =>                array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'gift' =>                        array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'gift_message' =>                array('type' => self::TYPE_STRING, 'validate' => 'isMessage'),
            'mobile_theme' =>                array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'total_discounts' =>            array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'total_discounts_tax_incl' =>    array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'total_discounts_tax_excl' =>    array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'total_paid' =>                array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'total_paid_tax_incl' =>        array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'total_paid_tax_excl' =>        array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'total_paid_real' =>            array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'total_products' =>            array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'total_products_wt' =>            array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'total_shipping' =>            array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'total_shipping_tax_incl' =>    array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'total_shipping_tax_excl' =>    array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'carrier_tax_rate' =>            array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'total_wrapping' =>            array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'total_wrapping_tax_incl' =>    array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'total_wrapping_tax_excl' =>    array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'round_mode' =>                    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'round_type' =>                    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'shipping_number' =>            array('type' => self::TYPE_STRING, 'validate' => 'isTrackingNumber'),
            'conversion_rate' =>            array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat', 'required' => true),
            'invoice_number' =>            array('type' => self::TYPE_INT),
            'delivery_number' =>            array('type' => self::TYPE_INT),
            'invoice_date' =>                array('type' => self::TYPE_DATE),
            'delivery_date' =>                array('type' => self::TYPE_DATE),
            'valid' =>                        array('type' => self::TYPE_BOOL),
            'reference' =>                    array('type' => self::TYPE_STRING),
            'date_add' =>                    array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' =>                    array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'address_ip' =>                 array('type' => self::TYPE_STRING, 'validate' => 'isAnything'),
            'user_agent' =>                 array('type' => self::TYPE_STRING, 'validate' => 'isAnything'),
        ),
    );
	

	public function getUserIpAddr(){
		if(!empty($_SERVER['HTTP_CLIENT_IP'])){
		    //ip from share internet
		    $ip = $_SERVER['HTTP_CLIENT_IP'];
		}elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
		    //ip pass from proxy
		    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}else{
		    $ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
    }
        
    public function getEmailSupplier($proveedor){
        $sql = 'SELECT p.`email` FROM `'._DB_PREFIX_.'supplier` AS p
                        WHERE p.`id_supplier` = '. $proveedor ;
        $supplier_ids = array();
        foreach (Db::getInstance()->executeS($sql) as $row) {
                $supplier_ids[] = $row['email'];
        }
        return $supplier_ids;
    }

    public static function getCustomerOrders($id_customer, $show_hidden_status = false, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
        SELECT o.*, s.name, s.id_supplier, (SELECT SUM(od.`product_quantity`) FROM `'._DB_PREFIX_.'order_detail` od WHERE od.`id_order` = o.`id_order`) nb_products
        FROM `'._DB_PREFIX_.'orders` o
        LEFT JOIN `'._DB_PREFIX_.'supplier` s ON (s.`id_supplier` = o.`id_supplier`)
        WHERE o.`id_customer` = '.(int)$id_customer.
        Shop::addSqlRestriction(Shop::SHARE_ORDER).'
        GROUP BY o.`id_order`
        ORDER BY o.`date_add` DESC');
        if (!$res) {
            return array();
        }

        foreach ($res as $key => $val) {
            $res2 = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
				SELECT os.`id_order_state`, osl.`name` AS order_state, os.`invoice`, os.`color` as order_state_color
				FROM `'._DB_PREFIX_.'order_history` oh
				LEFT JOIN `'._DB_PREFIX_.'order_state` os ON (os.`id_order_state` = oh.`id_order_state`)
				INNER JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = '.(int)$context->language->id.')
			WHERE oh.`id_order` = '.(int)$val['id_order'].(!$show_hidden_status ? ' AND os.`hidden` != 1' : '').'
				ORDER BY oh.`date_add` DESC, oh.`id_order_history` DESC
			LIMIT 1');

            if ($res2) {
                $res[$key] = array_merge($res[$key], $res2[0]);
            }
        }
        return $res;
    }

    public function getOrdersTotalTotalPaid()
    {
        return Db::getInstance()->getValue('
            SELECT SUM(total_paid_tax_incl)
            FROM `'._DB_PREFIX_.'orders`
            WHERE `reference` = \''.pSQL($this->reference).'\'
            AND `id_cart` = '.(int)$this->id_cart.
          ' AND `id_supplier` IN (SELECT id_supplier FROM nffr_supplier where proceso_cobro=1 )');
    }

   /* public function getOrdersTotalTotalPaid()
    {
        return Db::getInstance()->getValue('
            SELECT SUM(id_supplier)
            FROM `'._DB_PREFIX_.'orders`
            WHERE `reference` = \''.pSQL($this->reference).'\'
            AND `id_cart` = '.(int)$this->id_cart);
    }*/ 
}