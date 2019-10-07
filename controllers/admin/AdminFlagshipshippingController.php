<?php

include _PS_MODULE_DIR_.'flagshipshipping/flagshipshipping.php';

class AdminFlagshipshippingController extends ModuleAdminController {


    public function __construct()
    {
        parent::__construct();
        $this->bootstrap = true;
        $this->id_lang = $this->context->language->id;
        $this->default_form_language = $this->context->language->id;
    }

    public function initContent()
    {
        parent::initContent();
        $template_file = _PS_MODULE_DIR_. 'flagshipshipping/views/templates/admin/convert.tpl';
        $this->context->smarty->assign(array(
            'SMARTSHIP_WEB_URL' => SMARTSHIP_WEB_URL
        ));

        $content = $this->context->smarty->fetch($template_file);
        $this->context->smarty->assign(array(
            'content' =>  $content,
        ));
    } 
}