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



/**

 * Creamos la variable customerDefaultGroup que será usada para ocultar los

 * precios por volúmenes para los clientes de Florida

 */

class SearchController extends SearchControllerCore

{

    public $php_self = 'search';

    public $instant_search;

    public $ajax_search;



    /**

     * Initialize search controller

     * @see FrontController::init()

     */

    public function init()

    {

        parent::init();



        $this->instant_search = Tools::getValue('instantSearch');



        $this->ajax_search = Tools::getValue('ajaxSearch');



        if ($this->instant_search || $this->ajax_search) {

            $this->display_header = false;

            $this->display_footer = false;

        }

    }



    /**

     * Assign template vars related to page content

     * @see FrontController::initContent()

     */

    public function initContent()

    {

        parent::initContent();

        

        require_once("apps/advanced-part-finder/db/DatamasterConnection.php");

        require_once("apps/advanced-part-finder/models/Datamaster.php");

        

        $part = trim($_GET["search_query"]);

        $language = $this->context->language->id;

        $group = $this->context->customer->id_default_group;



        $datamaster = new Datamaster();

        $parts = $datamaster->getParts($part);



        if (! empty($parts)) {
 
            $datamasterPartProducts = $datamaster->getPartProducts($parts, $language, $group);

            $qpsProducts = $datamaster->searchQPSProduct($part, $language, $group);



            $duplicateValue = false;

            foreach ($qpsProducts as $qpsProduct) {

                foreach ($datamasterPartProducts as $datamasterPartProduct) {

                    if ($qpsProduct['id_product'] === $datamasterPartProduct['id_product'])

                        $duplicateValue = true;

                }



                if (! $duplicateValue)

                    $datamasterPartProducts[] = $qpsProduct;



                $duplicateValue = false;

            }

    

            if (empty($datamasterPartProducts)) {

                $datamasterPartProducts = null;

            } else if (count($datamasterPartProducts) == 1 && $datamasterPartProducts[0]['active']) {

                Tools::redirect('index.php?controller=product&id_product=' . $datamasterPartProducts[0]['id_product'] . '&search_query=' . $part);

            }

        } else {  

            // Si no consigue el part, debemos buscar en la BDD de PS

            $datamasterPartProducts = $datamaster->searchQPSProduct($part, $language, $group);



            if (empty($datamasterPartProducts)) {
                
                $datamasterPartProducts = $datamaster->searchQPSProductDescrip($part, $language, $group);

                if (empty($datamasterPartProducts)) {
                    $datamasterPartProducts = null;
                }else if (count($datamasterPartProducts) == 1 && $datamasterPartProducts[0]['active']) {

                    Tools::redirect('index.php?controller=product&id_product=' . $datamasterPartProducts[0]['id_product'] . '&search_query=' . $part);
    
                } 
            } else if (count($datamasterPartProducts) == 1 && $datamasterPartProducts[0]['active']) {

                Tools::redirect('index.php?controller=product&id_product=' . $datamasterPartProducts[0]['id_product'] . '&search_query=' . $part);

            }

        } 



        $this->context->smarty->assign(array(

            'products' => $datamasterPartProducts,

            'reference' => $part,

        ));



        $this->setTemplate(_PS_THEME_DIR_.'part-finder-results.tpl');

    }



    public function displayHeader($display = true)

    {

        if (!$this->instant_search && !$this->ajax_search) {

            parent::displayHeader();

        } else {

            $this->context->smarty->assign('static_token', Tools::getToken(false));

        }

    }



    public function displayFooter($display = true)

    {

        if (!$this->instant_search && !$this->ajax_search) {

            parent::displayFooter();

        }

    }



    public function setMedia()

    {

        parent::setMedia();



        if (!$this->instant_search && !$this->ajax_search) {

            $this->addCSS(_THEME_CSS_DIR_.'product_list.css');

            Tools::addCSS(_THEME_CSS_DIR_.'application-search-results.css','all');

            Tools::addCSS(_THEME_CSS_DIR_.'datatables.min.css','all');

            Tools::addJS(_THEME_JS_DIR_.'datatables.min.js');

        }

    }

}

