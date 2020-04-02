<?php

class AdminProductsController extends AdminProductsControllerCore
{ 
     
    protected $default_tab = 'Images';

      public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'product';
        $this->className = 'Product';
        $this->lang = true;
        $this->explicitSelect = true;
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?')
            )
        );
        if (!Tools::getValue('id_product')) {
            $this->multishop_context_group = false;
        }

        AdminController::__construct();

        $this->imageType = 'jpg';
        $this->_defaultOrderBy = 'position';
        $this->max_file_size = (int)(Configuration::get('PS_LIMIT_UPLOAD_FILE_VALUE') * 1000000);
        $this->max_image_size = (int)Configuration::get('PS_PRODUCT_PICTURE_MAX_SIZE');
        $this->allow_export = true;

        // @since 1.5 : translations for tabs
        $this->available_tabs_lang = array(
            'Informations' => $this->l('Information'),
            'Pack' => $this->l('Pack'),
            'VirtualProduct' => $this->l('Virtual Product'),
            'Prices' => $this->l('Prices'),
            'Seo' => $this->l('SEO'),
            'Images' => $this->l('Images'),
            'Associations' => $this->l('Associations'),
            'Shipping' => $this->l('Shipping'),
            'Combinations' => $this->l('Combinations'),
            'Features' => $this->l('Features'),
            'Customization' => $this->l('Customization'),
            'Attachments' => $this->l('Attachments'),
            'Quantities' => $this->l('Quantities'),
            'Suppliers' => $this->l('Suppliers'),
            'Warehouses' => $this->l('Warehouses'),
        );

        $this->available_tabs = array('Quantities' => 6, 'Warehouses' => 14);
        if ($this->context->shop->getContext() != Shop::CONTEXT_GROUP) {
            $this->available_tabs = array_merge($this->available_tabs, array(
                'Informations' => 0,
                'Pack' => 7,
                'VirtualProduct' => 8,
                'Prices' => 1,
                'Seo' => 2,
                'Associations' => 3,
                'Images' => 9,
                'Shipping' => 4,
                'Combinations' => 5,
                'Features' => 10,
                'Customization' => 11,
                'Attachments' => 12,
                'Suppliers' => 13,
            ));
        }

        // Sort the tabs that need to be preloaded by their priority number
        asort($this->available_tabs, SORT_NUMERIC);

        /* Adding tab if modules are hooked */
        $modules_list = Hook::getHookModuleExecList('displayAdminProductsExtra');
        if (is_array($modules_list) && count($modules_list) > 0) {
            foreach ($modules_list as $m) {
                $this->available_tabs['Module'.ucfirst($m['module'])] = 23;
                $this->available_tabs_lang['Module'.ucfirst($m['module'])] = Module::getModuleName($m['module']);
            }
        }

        if (Tools::getValue('reset_filter_category')) {
            $this->context->cookie->id_category_products_filter = false;
        }
        if (Shop::isFeatureActive() && $this->context->cookie->id_category_products_filter) {
            $category = new Category((int)$this->context->cookie->id_category_products_filter);
            if (!$category->inShop()) {
                $this->context->cookie->id_category_products_filter = false;
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminProducts'));
            }
        }
        /* Join categories table */
        if ($id_category = (int)Tools::getValue('productFilter_cl!name')) {
            $this->_category = new Category((int)$id_category);
            $_POST['productFilter_cl!name'] = $this->_category->name[$this->context->language->id];
        } else {
            if ($id_category = (int)Tools::getValue('id_category')) {
                $this->id_current_category = $id_category;
                $this->context->cookie->id_category_products_filter = $id_category;
            } elseif ($id_category = $this->context->cookie->id_category_products_filter) {
                $this->id_current_category = $id_category;
            }
            if ($this->id_current_category) {
                $this->_category = new Category((int)$this->id_current_category);
            } else {
                $this->_category = new Category();
            }
        }

        $join_category = false;
        if (Validate::isLoadedObject($this->_category) && empty($this->_filter)) {
            $join_category = true;
        }

        $this->_join .= '
        LEFT JOIN `'._DB_PREFIX_.'stock_available` sav ON (sav.`id_product` = a.`id_product` AND sav.`id_product_attribute` = 0
        '.StockAvailable::addSqlShopRestriction(null, null, 'sav').') ';

        $alias = 'sa';
        $alias_image = 'image_shop';

        $id_shop = Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP? (int)$this->context->shop->id : 'a.id_shop_default';
        $this->_join .= ' JOIN `'._DB_PREFIX_.'product_shop` sa ON (a.`id_product` = sa.`id_product` AND sa.id_shop = '.$id_shop.')
                LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON ('.$alias.'.`id_category_default` = cl.`id_category` AND b.`id_lang` = cl.`id_lang` AND cl.id_shop = '.$id_shop.')
                LEFT JOIN `'._DB_PREFIX_.'shop` shop ON (shop.id_shop = '.$id_shop.')
                LEFT JOIN `'._DB_PREFIX_.'image_shop` image_shop ON (image_shop.`id_product` = a.`id_product` AND image_shop.`cover` = 1 AND image_shop.id_shop = '.$id_shop.')
                LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_image` = image_shop.`id_image`)
                LEFT JOIN `'._DB_PREFIX_.'product_download` pd ON (pd.`id_product` = a.`id_product` AND pd.`active` = 1)
                LEFT JOIN  '._DB_PREFIX_.'supplier sup ON(sup.id_supplier = a.id_supplier)';

        $this->_select .= 'shop.`name` AS `shopname`, a.`id_shop_default`, CASE a.on_sale WHEN 0 THEN "No" WHEN 1 THEN "Si" END AS en_oferta, ';
        $this->_select .= $alias_image.'.`id_image` AS `id_image`, cl.`name` AS `name_category`, '.$alias.'.`price`, 0 AS `price_final`, a.`is_virtual`, pd.`nb_downloadable`, sav.`quantity` AS `sav_quantity`, '.$alias.'.`active`, IF(sav.`quantity`<=0, 1, 0) AS `badge_danger`,a.id_supplier as co_proveedor ,sup.name as nombre_proveedor, 
            a.id_product as volume_discount';

        if ($join_category) {
            $this->_join .= ' INNER JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_product` = a.`id_product` AND cp.`id_category` = '.(int)$this->_category->id.') ';
            $this->_select .= ' , cp.`position`, ';
        }
        $this->_use_found_rows = false;
        $this->_group = '';

        $this->fields_list = array();

        $this->fields_list['id_product'] = array(
            'title' => $this->l('ID'),
            'align' => 'center',
            'class' => 'fixed-width-xs',
            'type' => 'int'
        );
        
        $this->fields_list['image'] = array(
            'title' => $this->l('Image'),
            'align' => 'center',
            'image' => 'p',
            'orderby' => false,
            'filter' => false,
            'search' => false
        );
        $this->fields_list['name'] = array(
            'title' => $this->l('Name'),
            'filter_key' => 'b!name'
        );
        $this->fields_list['reference'] = array(
            'title' => $this->l('Reference'),
            'align' => 'left',
        );

        if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) {
            $this->fields_list['shopname'] = array(
                'title' => $this->l('Default shop'),
                'filter_key' => 'shop!name',
            );
        } else {
            $this->fields_list['name_category'] = array(
                'title' => $this->l('Category'),
                'filter_key' => 'cl!name',
            );
        }
        $this->fields_list['price'] = array(
            'title' => $this->l('Base price'),
            'type' => 'price',
            'align' => 'text-right',
            'filter_key' => 'a!price'
        );
        $this->fields_list['price_final'] = array(
            'title' => $this->l('Final price'),
            'type' => 'price',
            'align' => 'text-right',
            'havingFilter' => true,
            'orderby' => false,
            'search' => false
        );

        $this->fields_list['volume_discount'] = array(
            'title' => $this->l('Volume discount'),
            'align' => 'text-center',
            'callback' => 'getDiscountByVolume',
            'search' => false,
        );


        if (Configuration::get('PS_STOCK_MANAGEMENT')) {
            $this->fields_list['sav_quantity'] = array(
                'title' => $this->l('Quantity'),
                'type' => 'int',
                'align' => 'text-right',
                'filter_key' => 'sav!quantity',
                'orderby' => true,
                'badge_danger' => true,
                //'hint' => $this->l('This is the quantity available in the current shop/group.'),
            );
        }

        $this->fields_list['active'] = array(
            'title' => $this->l('Status'),
            'active' => 'status',
            'filter_key' => $alias.'!active',
            'align' => 'text-center',
            'type' => 'bool',
            'class' => 'fixed-width-sm',
            'orderby' => false
        );

        if ($join_category && (int)$this->id_current_category) {
            $this->fields_list['position'] = array(
                'title' => $this->l('Position'),
                'filter_key' => 'cp!position',
                'align' => 'center',
                'position' => 'position'
            );
        }


           $this->fields_list['en_oferta'] = array(
            'title' => $this->l('in offer?'),
            'align' => 'text-center',
            'filter_key' => 'a!on_sale',
            'type' => 'bool',
        );
           $array_supplier=array();
            $resul = Supplier::getAllSupplierName();
            foreach ($resul as $fila) {               
                $array_supplier[$fila['id_supplier']] = $fila['name'];       
            }

            $this->fields_list['nombre_proveedor'] = array(
            'title' => $this->l('Supplier'),
            'align' => 'text-center',
            'filter_key' => 'sup!id_supplier',
            'type' => 'select',
            'list' => $array_supplier
        );

            

    }

    public static function getDiscountByVolume($id_product)
    {
         
        $volume_discount="";
        $res = SpecificPrice::getVolumeDiscount($id_product);
        if ($res) {
            foreach ($res as $reg) {
             $volume_discount .= "+".(int)$reg['from_quantity']."(".((float)$reg['reduction']*100)."%)&nbsp;&nbsp;";
            }
        }
        return  $volume_discount;
    }



}
