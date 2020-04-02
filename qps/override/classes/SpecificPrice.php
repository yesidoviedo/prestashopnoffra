<?php
/*
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class SpecificPrice extends SpecificPriceCore
{

 	public static function getVolumeDiscount($id_product, $id_product_attribute = false, $id_cart = false)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT from_quantity,reduction
			FROM `'._DB_PREFIX_.'specific_price`
			WHERE `id_product` = '.(int)$id_product.
            ($id_product_attribute ? ' AND id_product_attribute = '.(int)$id_product_attribute : '').'
			AND id_cart = '.(int)$id_cart.' 
			Group by from_quantity,reduction 
			Order by from_quantity');


    }

}
