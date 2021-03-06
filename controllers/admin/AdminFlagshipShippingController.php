<?php
/**
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registred Trademark & Property of PrestaShop SA
*/

include _PS_MODULE_DIR_.'flagshipshipping/flagshipshipping.php';

class AdminFlagshipShippingController extends ModuleAdminController
{

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
        $testEnv = Configuration::get('flagship_test_env');
        $webUrl = SMARTSHIP_WEB_URL;
        if($testEnv == 1){
            $webUrl = SMARTSHIP_TEST_WEB_URL;
        }
        $this->context->smarty->assign(array(
            'SMARTSHIP_WEB_URL' => $webUrl
        ));

        $content = $this->context->smarty->fetch($template_file);
        $this->context->smarty->assign(array(
            'content' =>  $content,
        ));
    }
}
