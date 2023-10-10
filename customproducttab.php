<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class CustomProductTab extends Module {

    public function __construct()
    {
        $this->name = 'customproducttab';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Christos Patakias';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Custom Product Tab');
        $this->description = $this->l('Adds a custom tab to product edit page.');

        $this->confirmUnistall = $this->l('Are you sure you want to unistall this module;');

        if (!configuration::get('CUSTOMPRODUCTTAB')) {
            $this->warning = $this->l('No name provided');
        }
    }

    public function install()
    {
        if (Shop::isFeatureActive())
            Shop::setContext(Shop::CONTEXT_ALL);

        return parent::install() &&
            $this->registerHook('displayAdminProductExtra') &&
            $this->registerHook('DisplayProductTabContent') &&
            Configuration::updateValue('CUSTOMPRODUCTTAB', 'CustomProductTab');;
    }

    public function unistall()
    {
        if (!parent::unistall() ||
            !configuration::deleteByName('CUSTOMPRODUCTTAB')
        ) {
            return false;
        }
        return true;
    }

    public function hookDisplayAdminProductExtra($params)
    {
        $product = new Product((int)$params['id_product']);
        if (!Validate::isLoadedObject($product)) {
            return;
        }

        $this->context->smarty->assign(array(
           'product' => $product,
        ));

        return $this->display(__FILE__, 'views/templates/admin/product_tab.tpl');
    }

    public function hookDisplayProductTabContent($params)
    {
        $product = $params['product'];
        if (!Validate::isLoadedObject($product)) {
            return '';
        }

        $field1 = $product->field1;
        $field2 = $product->field2;
        $field3 = $product->field3;

        $smarty = Context::getContext()->smarty;
        $smarty->assign(array(
            'field1' => $field1,
            'field2' => $field2,
            'field3' => $field3,
        ));

        return $this->display(__FILE__, 'views/templates/hook/product_tab_content.tpl');
    }
}