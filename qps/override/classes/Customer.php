<?php 



class Customer extends CustomerCore

{



    public $id_employee; //codigo de empleado. clave foranea de la tabla ps_employee.

    public $id_cliente;//codigo de cliente 

    public $cxp;

    public $credit_limit;



    public static $definition = array(

        'table' => 'customer',

        'primary' => 'id_customer',

        'fields' => array(

            'secure_key' =>                array('type' => self::TYPE_STRING, 'validate' => 'isMd5', 'copy_post' => false),

            'lastname' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isName', 'required' => true, 'size' => 32),

            'firstname' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isName', 'required' => true, 'size' => 32),

            'email' =>                        array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'required' => true, 'size' => 128),

            'passwd' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isPasswd', 'required' => true, 'size' => 32),

            'last_passwd_gen' =>            array('type' => self::TYPE_STRING, 'copy_post' => false),

            'id_gender' =>                    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),

            'birthday' =>                    array('type' => self::TYPE_DATE, 'validate' => 'isBirthDate'),

            'newsletter' =>                array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),

            'newsletter_date_add' =>        array('type' => self::TYPE_DATE,'copy_post' => false),

            'ip_registration_newsletter' =>    array('type' => self::TYPE_STRING, 'copy_post' => false),

            'optin' =>                        array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),

            'website' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isUrl'),

            'company' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),

            'siret' =>                        array('type' => self::TYPE_STRING, 'validate' => 'isSiret'),

            'ape' =>                        array('type' => self::TYPE_STRING, 'validate' => 'isApe'),

            'outstanding_allow_amount' =>    array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat', 'copy_post' => false),

            'show_public_prices' =>            array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),

            'id_risk' =>                    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'copy_post' => false),

            'max_payment_days' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'copy_post' => false),

            'active' =>                      array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),

            'deleted' =>                     array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),

            'note' =>                        array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml', 'size' => 65000, 'copy_post' => false),

            'is_guest' =>                    array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),

            'id_shop' =>                     array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'copy_post' => false),

            'id_shop_group' =>               array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'copy_post' => false),

            'id_default_group' =>            array('type' => self::TYPE_INT, 'copy_post' => false),

            'id_lang' =>                     array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'copy_post' => false),

            'date_add' =>                    array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),

            'date_upd' =>                    array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),

            'id_employee' =>                 array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt','required' => false),

            'id_cliente' =>                  array('type' => self::TYPE_STRING, 'validate' => 'isAnything'),

            'credit_limit' =>                array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat', 'copy_post' => false),

            'cxp' =>                         array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat', 'copy_post' => false),

        ),

    );





public function getInformationSaler(){



    return Db::getInstance()->getRow('

            SELECT *

            FROM '._DB_PREFIX_.'employee emp

            WHERE emp.`id_employee` = '.(int)$this->id_employee

        );



}



 /**

     * Light back office search for customers

     *

     * @param string $query Searched string

     * @param null|int $limit Limit query results

     * @return array|false|mysqli_result|null|PDOStatement|resource Corresponding customers

     * @throws PrestaShopDatabaseException.' '.

     */

    public static function searchByName($query, $limit = null)

    {



        $context = Context::getContext();

        

        $condicion="";



        if($context->employee->id_profile==4){ //si el usuario es vendedor

           $condicion = " AND id_employee=".$context->employee->id ;

        }



        $sql_base = 'SELECT *

                FROM `'._DB_PREFIX_.'customer`';



        $sql = '('.$sql_base.' WHERE `email` LIKE \'%'.pSQL($query).'%\' '.$condicion.' '.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER).')';



        $sql .= ' UNION ('.$sql_base.' WHERE `id_customer` = '.(int)$query.' '.$condicion.' '.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER).')';



        $sql .= ' UNION ('.$sql_base.' WHERE `lastname` LIKE \'%'.pSQL($query).'%\' '.$condicion.' '.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER).')';



        $sql .= ' UNION ('.$sql_base.' WHERE `firstname` LIKE \'%'.pSQL($query).'%\' '.$condicion.' '.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER).')';



        $sql .= ' UNION ('.$sql_base.' WHERE `company` LIKE \'%'.pSQL($query).'%\' '.$condicion.' '.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER).')';



        $sql .= ' UNION ('.$sql_base.' WHERE `id_cliente` LIKE \'%'.pSQL($query).'%\' '.$condicion.' '.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER).')';



        if ($limit) {

            $sql .= ' LIMIT 0, '.(int)$limit;

        }



        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

    }





}

 ?>