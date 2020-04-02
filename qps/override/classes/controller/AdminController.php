<?php 

class AdminController extends AdminControllerCore
{

    public function __construct()
    {
    parent::__construct();
    }


    protected function addPageHeaderToolBarModulesListButton()
    {
        $this->filterTabModuleList();

        
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->toolbar_title)) {
            $this->initToolbarTitle();
        }

        if (!is_array($this->toolbar_title)) {
            $this->toolbar_title = array($this->toolbar_title);
        }

        switch ($this->display) {
            case 'view':
                // Default cancel button - like old back link
                $back = Tools::safeOutput(Tools::getValue('back', ''));
                if (empty($back)) {
                    $back = self::$currentIndex.'&token='.$this->token;
                }
                if (!Validate::isCleanHtml($back)) {
                    die(Tools::displayError());
                }
                if (!$this->lite_display) {
                    $this->page_header_toolbar_btn['back'] = array(
                        'href' => $back,
                        'desc' => $this->l('Back to list')
                    );
                }
                $obj = $this->loadObject(true);
                if (Validate::isLoadedObject($obj) && isset($obj->{$this->identifier_name}) && !empty($obj->{$this->identifier_name})) {
                    array_pop($this->toolbar_title);
                    array_pop($this->meta_title);
                    $this->toolbar_title[] = is_array($obj->{$this->identifier_name}) ? $obj->{$this->identifier_name}[$this->context->employee->id_lang] : $obj->{$this->identifier_name};
                    $this->addMetaTitle($this->toolbar_title[count($this->toolbar_title) - 1]);
                }
                break;
            case 'edit':
                $obj = $this->loadObject(true);
                if (Validate::isLoadedObject($obj) && isset($obj->{$this->identifier_name}) && !empty($obj->{$this->identifier_name})) {
                    array_pop($this->toolbar_title);
                    array_pop($this->meta_title);
                    $this->toolbar_title[] = sprintf($this->l('Edit: %s'), (is_array($obj->{$this->identifier_name}) && isset($obj->{$this->identifier_name}[$this->context->employee->id_lang])) ? $obj->{$this->identifier_name}[$this->context->employee->id_lang] : $obj->{$this->identifier_name});
                    $this->addMetaTitle($this->toolbar_title[count($this->toolbar_title) - 1]);
                }
                break;
        }

        if (is_array($this->page_header_toolbar_btn)
            && $this->page_header_toolbar_btn instanceof Traversable
            || count($this->toolbar_title)) {
            $this->show_page_header_toolbar = true;
        }

        if (empty($this->page_header_toolbar_title)) {
            $this->page_header_toolbar_title = $this->toolbar_title[count($this->toolbar_title) - 1];
        }

        $this->addPageHeaderToolBarModulesListButton();

        //cramirez. desabilitando el boton help en todo el backoffice.
        
        // $this->context->smarty->assign('help_link', 'http://help.prestashop.com/'.Language::getIsoById($this->context->employee->id_lang).'/doc/'
        //    .Tools::getValue('controller').'?version='._PS_VERSION_.'&country='.Language::getIsoById($this->context->employee->id_lang));

    }





}


?>