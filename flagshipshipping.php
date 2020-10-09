<?php
/**
 * 2007-2019 PrestaShop
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    include_once __DIR__ . '/vendor/autoload.php';
}

use Flagship\Shipping\Flagship;

//NO Tailing slashes please
define('SMARTSHIP_WEB_URL', 'https://smartship-ng.flagshipcompany.com');
define('SMARTSHIP_API_URL', 'https://api.smartship.io');
define('TEST_API_URL', 'https://test-api.smartship.io');

class FlagshipShipping extends CarrierModule
{
    public $id_carrier;
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'flagshipshipping';
        $this->tab = 'shipping_logistics';
        $this->version = '1.0.12';
        $this->author = 'FlagShip Courier Solutions';
        $this->need_instance = 0;

        $this->logger = new FileLogger(0); //0 == debug level, logDebug() won’t work without this.
        $this->logger->setFilename(_PS_ROOT_DIR_."/var/logs/flagship.log");

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('FlagShip For PrestaShop');
        $this->description = $this->l('Send your shipments with FlagShip now.');
        $this->description .= $this->l(' Drop the hassle of figuring out the best prices.');
        $this->description .= $this->l(' Get real time prices from major courier service providers.');
        $this->description .= $this->l(' Your customers will never have to deal with a delayed delivery again.');
        $this->description .= $this->l(' A happy customer is a happy You!');

        $this->confirmUninstall = $this->l('Uninstalling FlagShip will remove all shipments.');
        $this->confirmUninstall .= $this->l(' Are you sure you want to uninstall?');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);

        $this->registerHook('displayBackOfficeOrderActions');
        $this->registerHook('actionValidateCustomerAddressForm');
        $this->registerHook('actionCartSave');
        $this->registerHook('displayAdminAfterHeader');
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */

    public function install()
    {
        if (extension_loaded('curl') == false) {
            $this->_errors[] = $this->l('You have to enable the cURL extension on your server to install this module');
            return false;
        }

        Db::getInstance()->execute('
                CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'flagship_shipping` (
                `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `id_order` INT(10) UNSIGNED NOT NULL,
                `flagship_shipment_id` INT(10) UNSIGNED NULL,
                PRIMARY KEY (`id`)
                )
            ');

        Db::getInstance()->execute('
                CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'flagship_boxes` (
                `id` INT(2) UNSIGNED NOT NULL AUTO_INCREMENT,
                `model` VARCHAR(25) NOT NULL,
                `length` INT(2) UNSIGNED NOT NULL,
                `width` INT(2) UNSIGNED NOT NULL,
                `height` INT(2) UNSIGNED NOT NULL,
                `weight` FLOAT(4,2) UNSIGNED NOT NULL,
                `max_weight` FLOAT(4,2) UNSIGNED NOT NULL,
                PRIMARY KEY(`id`)
                )
            ');

        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'AdminFlagshipShipping';
        $tab->position = 3;
        $tab->name = [];
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'Convert FlagShip Shipments';
        }
        $tab->id_parent = (int) Tab::getIdFromClassName('SELL');
        $tab->module = $this->name;
        $tab->add();
        $tab->save();
        $this->logger->logDebug("Flagship for prestashop installed");
        return parent::install();
    }

    public function uninstall()
    {
        $id_tab = (int) Tab::getIdFromClassName('AdminFlagshipShipping');
        $tab = new Tab($id_tab);
        $tab->delete();

        Configuration::deleteByName('flagship_api_token');
        Configuration::deleteByName('flagship_fee');
        Configuration::deleteByName('flagship_markup');
        Configuration::deleteByName('flagship_residential');
        Configuration::deleteByName('flagship_test_env');

        Db::getInstance()->execute('DROP TABLE `'._DB_PREFIX_.'flagship_shipping`');
        Db::getInstance()->execute('DROP TABLE `'._DB_PREFIX_.'flagship_boxes`');
        Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'carrier` WHERE external_module_name = "flagshipshipping"');
        $this->logger->logDebug("Flagship for prestashop uninstalled");
        return parent::uninstall();
    }

    public function hookDisplayAdminAfterHeader(array $params)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://api.github.com/repos/flagshipcompany/flagship-for-prestashop/releases/latest",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_USERAGENT => " ",
        ));

        $response = json_decode(curl_exec($curl));

        curl_close($curl);
        $latestTag = Tools::substr($response->tag_name, 1);
        $latestTagNumber = strrchr($latestTag,"."); 
        $versionNumber = strrchr($this->version, ".");

        $tagMismatch = $latestTagNumber > $versionNumber ? 1 : 0;

        $this->context->smarty->assign(array(
            'tagMismatch' => $tagMismatch
        ));
        return $this->display(__FILE__,'notification.tpl');
    }

    /**
     * Load the configuration form
     */
    public function getContent() : string
    {
        /**
         * If values have been submitted in the form, process.
         */
        $output = '';

        if (((bool)Tools::isSubmit('submit'.$this->name.'Module')) == true) {
            $output .= $this->postProcess();
        }

        if (((bool)Tools::isSubmit('submit'.$this->name.'BoxModule')) == true) {
            $output .= $this->insertBoxDetails();
        }

        $this->context->smarty->assign('module_dir', $this->_path);
        $this->context->smarty->assign('boxes', $this->getBoxesString());
        $this->context->smarty->assign('units', $this->getWeightUnits());
        $output .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/note.tpl');
        $output .= $this->renderForm();
        $output .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/boxes.tpl');
        $output .= $this->renderBoxesForm();
        return $output;
    }

    public function hookDisplayBackOfficeOrderActions(array $params)
    {
        $id_order = $params["id_order"];
        $link = new Link();

        $shipmentId = $this->getShipmentId($id_order);
        $shipmentFlag = is_null($shipmentId) ? 0 : $shipmentId;

        $this->context->smarty->assign(array(
            'url' => $link->getAdminLink('AdminFlagshipShipping'),
            'shipmentFlag' => $shipmentFlag,
            'SMARTSHIP_WEB_URL' => SMARTSHIP_WEB_URL,
            'orderId' => $id_order,
            'img_dir' => _PS_IMG_DIR_,
        ));
        return $this->display(__FILE__, 'flagship.tpl');
    }

    public function prepareShipment(string $token, int $orderId) : string
    {
        $url = $this->getBaseUrl();
        try {
            $storeName = $this->context->shop->name;
            $flagship = new Flagship($token, $url, 'Prestashop', _PS_VERSION_);
            $payload = $this->getPayloadForShipment($orderId);
            $this->logger->logDebug("Payload for prepare shipment: ".json_encode($payload));
            $prepareShipment = $flagship->prepareShipmentRequest($payload)->setStoreName($storeName)->setOrderId($orderId);
            $prepareShipment = $prepareShipment->execute();
            $shipmentId = $prepareShipment->shipment->id;
            $this->logger->logDebug("Flagship shipment prepared for order id: ".$orderId);
            $this->updateOrder($shipmentId, $orderId);
            return $this->displayConfirmation('FlagShip Shipment Prepared : '.$shipmentId);
        } catch (Exception $e) {
            return $this->displayError($e->getMessage());
        }
    }

    public function updateShipment(string $token, int $orderId, int $shipmentId) : string
    {
        $url = $this->getBaseUrl();
        try {
            $storeName = $this->context->shop->name;
            $flagship = new Flagship($token, $url, 'Prestashop', _PS_VERSION_);
            $payload = $this->getPayloadForShipment($orderId);
            $this->logger->logDebug("Payload for upload shipment: ".json_encode($payload));
            $updateShipment = $flagship->editShipmentRequest($payload, $shipmentId)->setStoreName($storeName)->setOrderId($orderId);
            $updatedShipment = $updateShipment->execute();
            $updatedShipmentId = $updatedShipment->shipment->id;
            return $this->displayConfirmation('Updated! FlagShip Shipment: '.$updatedShipmentId);
        } catch (Exception $e) {
            return $this->displayError($e->getMessage());
        }
    }

    public function getOrderShippingCost($params, $shipping_cost) //do not use return type or argument type
    {
        $id_address_delivery = Context::getContext()->cart->id_address_delivery;
        $address = new Address($id_address_delivery);
        if ($id_address_delivery == 0) {
            return $shipping_cost;
        }
        $carrier = new Carrier($this->id_carrier);
        if (isset(Context::getContext()->cookie->rates)) {
            $rate = explode(",", Context::getContext()->cookie->rate);
            $couriers = $this->getCouriers($rate);
            return !in_array($carrier->name, $couriers) ? false : $this->getShippingCost($rate, $carrier);
        }

        $token = Configuration::get('flagship_api_token');
        $url = $this->getBaseUrl();
        $flagship = new Flagship($token, $url, 'Prestashop', _PS_VERSION_);
        $payload = $this->getPayload($address);

        if (!isset(Context::getContext()->cookie->rates)) {
            $storeName = $this->context->shop->name;
            $this->logger->logDebug("Quotes payload: ".json_encode($payload));
            $rates = $flagship->createQuoteRequest($payload)->setStoreName($storeName)->execute()->sortByPrice();
            Context::getContext()->cookie->rates = 1;
            $ratesArray = $this->prepareRates($rates);
            $str = $this->getRatesString($ratesArray);
            Context::getContext()->cookie->rate = $str;
        }
        return $shipping_cost;
    }

    protected function getRatesString(array $ratesArray) : string
    {
        $str = '';
        foreach ($ratesArray as $value) {
            $str .= implode("-", $value).",";
        }
        $str = rtrim($str);
        return $str;
    }

    protected function getShippingCost(array $rate, Carrier $carrier) : float
    {
        $shipping_cost = 0.00;
        $costs = [];
        $shippingCosts = [];
        foreach ($rate as $value) {
            $cost = floatVal(Tools::substr($value, strpos($value, "-")+1));
            $cost += floatVal((Configuration::get("flagship_markup")/100) * $cost);
            $cost += floatVal(Configuration::get('flagship_fee'));
            $taxes = ltrim(strrchr($value, "-"), "-");
            $cost += floatVal($taxes);
            $shipping_cost=Tools::substr($value, 0, strpos($value, "-")) == $carrier->name ? $cost : $shipping_cost;
        }
        return $shipping_cost;
    }

    protected function getCouriers($rate){
        $couriers = [];
        foreach ($rate as $value) {
            $couriers[] = Tools::substr($value, 0, strpos($value, "-"));
        }
        return $couriers;
    }

    public function getOrderShippingCostExternal($params) : bool
    {
        return true;
    }

    public function hookActionValidateCustomerAddressForm() : bool
    {
        unset(Context::getContext()->cookie->rates);
        unset(Context::getContext()->cookie->rate);
        return true;
    }

    public function hookActionCartSave() : bool
    {

        unset(Context::getContext()->cookie->rates);
        unset(Context::getContext()->cookie->rate);
        return true;
    }

    public function getBoxesString() : string
    {
        $boxes = '';
        $query = new DbQuery();
        $query->select('*')->from('flagship_boxes');

        $rows = Db::getInstance()->executeS($query);

        if (count($rows) == 0) {
            $boxes = 'No boxes set';
            return $boxes;
        }

        foreach ($rows as $row) {
            $boxes .= '<row id = "'.$row["id"].'"><a class="delete"';
            $boxes .= ' data-toggle="tooltip" title="Delete Box">';
            $boxes .= '<i class="icon icon-trash"></i></a>';
            $boxes .=' <strong>'.$row["model"].'</strong> : ';
            $boxes .= $row["length"].' x '.$row["width"].' x '.$row["height"];
            $boxes .= ' x '.$row["weight"].'<strong>Max Weight</strong> : ';
            $boxes .= $row["max_weight"].'</row><br/>';
        }
        return $boxes;
    }

    protected function getShipmentId(int $id_order) : ?int
    {
        $sql = new DbQuery();
        $sql->select('flagship_shipment_id');
        $sql->from('flagship_shipping', 'fs');
        $sql->where('fs.id_order = '.$id_order);
        $shipmentId = Db::getInstance()->executeS($sql);
        if (empty($shipmentId)) {
            return null;
        }
        return $shipmentId[0]['flagship_shipment_id'];
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm() : string
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitflagshipshippingModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm([$this->getConfigForm()]);
    }

    protected function renderBoxesForm() : string
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitflagshipshippingBoxModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm([$this->getBoxesForm()]);
    }

    protected function getPayloadForShipment(int $orderId) : array
    {
        $from = [
            "name"=>Configuration::get('PS_SHOP_NAME'),
            "attn"=>Configuration::get('PS_SHOP_NAME'),
            "address"=>Configuration::get('PS_SHOP_ADDR1'),
            "suite"=>Configuration::get('PS_SHOP_ADDR2'),
            "city"=>Configuration::get('PS_SHOP_CITY'),
            "country"=>Country::getIsoById(Configuration::get('PS_SHOP_COUNTRY_ID')),
            "state"=>$this->getStateCode(Configuration::get('PS_SHOP_STATE_ID')),
            "postal_code"=>Configuration::get('PS_SHOP_CODE'),
            "phone"=> Configuration::get('PS_SHOP_PHONE'),
            "is_commercial"=>true
        ];

        $order = new Order($orderId);
        $addressTo = new Address($order->id_address_delivery);
        $products = $order->getProducts();

        $name = empty($addressTo->company) ? $addressTo->firstname : $addressTo->company;
         $isCommercial = Configuration::get('flagship_residential') ? false : true;
        $to = [
            "name"=>$name,
            "attn"=>$addressTo->firstname,
            "address"=>$addressTo->address1,
            "suite"=>$addressTo->address2,
            "city"=>$addressTo->city,
            "country"=>Country::getIsoById((int)$addressTo->id_country),
            "state"=>$this->getStateCode((int)$addressTo->id_state),
            "postal_code"=>$addressTo->postcode,
            "phone"=> $addressTo->phone,
            "is_commercial"=>$isCommercial
        ];

        $package = $this->getPackages($order);

        $options = [
            "signature_required"=>false,
            "reference"=>"PrestaShop Order# ".$orderId
        ];

        $payment = [
            "payer"=>"F"
        ];

        $payload = [
            'from' => $from,
            'to'  => $to,
            'packages' => $package,
            'options' => $options,
            'payment' => $payment
        ];
        return $payload;
    }

    protected function getTotalWeight(array $products) : float
    {
        $total = 0;
        foreach ($products as $product) {
            $total += $product["weight"]*$product["product_quantity"];
        }
        if ($total<1) {
            $total = 1;
        }
        return $total;
    }

    protected function getWeightUnits() : string
    {
        if (Configuration::get('PS_WEIGHT_UNIT') === 'kg') {
            return 'metric';
        }
        return 'imperial';
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm() : array
    {
        return [
            'form' =>
            [
                'legend' =>
                [
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'col' => 4,
                        'type' => 'select',
                        'label' => $this->l('Test Environment'),
                        'name' => 'flagship_test_env',
                        'options' => [
                            'query' => [
                                [
                                    'key' => 0,
                                    'name' => 'No'
                                ],
                                [
                                    'key' => 1,
                                    'name' => 'Yes'
                                ]
                            ],
                            'id' => 'key',
                            'name' => 'name',
                        ]

                    ],
                    [
                        'col' => 4,
                        'type' => 'text',
                        'desc' => Configuration::get('flagship_api_token') ? 'API Token is set'
                            : $this->l('Enter API Token'),
                        'name' => 'flagship_api_token',
                        'label' => $this->l('API Token'),
                    ],
                    [
                        'col' => 4,
                        'type' => 'text',
                        'name' => 'flagship_markup',
                        'label' =>$this->l('Percentage Markup')
                    ],
                    [
                        'col' => 4,
                        'type' => 'text',
                        'label' => $this->l('Flat Handling Fee'),
                        'name' => 'flagship_fee'
                    ],
                    [
                        'col' => 4,
                        'type' => 'select',
                        'label' => $this->l('Residential Shipments'),
                        'name' => 'flagship_residential',
                        'options' => [
                            'query' => [
                                [
                                    'key' => 0,
                                    'name' => 'No'
                                ],
                                [
                                    'key' => 1,
                                    'name' => 'Yes'
                                ]
                            ],
                            'id' => 'key',
                            'name' => 'name',
                        ]
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];
    }

    protected function getBoxesForm() : array
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->l('Add New Box (Units: '.$this->getWeightUnits().')'),
                    'icon' => 'icon-plus-circle'
                ],
                'input' => [
                    [
                        'col' => 4,
                        'type' => 'text',
                        'name' => 'flagship_box_model',
                        'label' => $this->l('Box Model'),
                    ],
                    [
                        'col' => 4,
                        'type' => 'text',
                        'name' => 'flagship_box_length',
                        'label' => $this->l('Length'),
                    ],
                    [
                        'col' => 4,
                        'type' => 'text',
                        'name' => 'flagship_box_width',
                        'label' => $this->l('Width'),
                    ],
                    [
                        'col' => 4,
                        'type' => 'text',
                        'name' => 'flagship_box_height',
                        'label' => $this->l('Height'),
                    ],
                    [
                        'col' => 4,
                        'type' => 'text',
                        'name' => 'flagship_box_weight',
                        'label' => $this->l('Weight'),
                    ],
                    [
                        'col' => 4,
                        'type' => 'text',
                        'name' => 'flagship_box_max_weight',
                        'label' => $this->l('Max Weight'),
                    ]
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ]
            ]
        ];
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues() : array
    {
        $apiToken = Configuration::get('flagship_api_token') ? Configuration::get('flagship_api_token') : '';
        return [
            'flagship_test_env' => Configuration::get('flagship_test_env'),
            'flagship_api_token' => '',
            'flagship_markup' => Configuration::get('flagship_markup'),
            'flagship_fee' => Configuration::get('flagship_fee'),
            'flagship_residential' => Configuration::get('flagship_residential'),
            'flagship_box_model' => '',
            'flagship_box_length' => '',
            'flagship_box_width' => '',
            'flagship_box_height' => '',
            'flagship_box_weight' => '',
            'flagship_box_max_weight' => ''
        ];
    }

    /**
     * Save form data.
     */
    protected function postProcess() : string
    {

        $apiToken = empty(Tools::getValue('flagship_api_token')) ?
                Configuration::get('flagship_api_token') :
                Tools::getValue('flagship_api_token');

        $fee = empty(Tools::getValue('flagship_fee')) ? 0 : Tools::getValue('flagship_fee');
        $markup = empty(Tools::getValue('flagship_markup')) ? 0 : Tools::getValue('flagship_markup');
        $residential = empty(Tools::getValue('flagship_residential')) ? 0 :
                    Tools::getValue('flagship_residential');
        $testEnv = empty(Tools::getValue('flagship_test_env')) ? 0 : Tools::getValue('flagship_test_env');

        if (is_string(Configuration::get('flagship_fee')) && is_string(Configuration::get('flagship_api_token')) && is_string(Configuration::get('flagship_markup')) ) { //fields exist in db
            $feeFlag = $fee != Configuration::get('flagship_fee') ?
            Configuration::updateValue('flagship_fee', $fee) : 0 ;
            $markupFlag = $markup != Configuration::get('flagship_markup') ?
            Configuration::updateValue('flagship_markup', $markup) : 0 ;
            $residentialFlag = $residential != Configuration::get('flagship_residential') ?
            Configuration::updateValue('flagship_residential', $residential) : 0 ;
            $testEnvFlag = $testEnv != Configuration::get('flagship_test_env') ? Configuration::updateValue('flagship_test_env', $testEnv) : 0;

            return $this->displayConfirmation($this->getReturnMessage($apiToken, $testEnv, $feeFlag, $markupFlag, $residentialFlag));

        }

        if ($this->setApiToken($apiToken, $testEnv) && $this->setMarkup($markup) && $this->setHandlingFee($fee) && $this->setTestEnv($testEnv) && $this->setResidential($residential)) {
            $storeName = $this->context->shop->name;
            $url = $this->getBaseUrl();
            $flagship = new Flagship($apiToken, $url, 'Prestashop', _PS_VERSION_);
            $availableServices = $flagship->availableServicesRequest()->setStoreName($storeName)->execute();
            $this->prepareCarriers($availableServices);

            return $this->displayConfirmation($this->l('FlagShip Configured'));
        }
        return $this->displayWarning($this->l("Oops! Token is invalid or same token is set."));
    }

    protected function getReturnMessage(string $apiToken, int $testEnv, int $feeFlag, int $markupFlag, int $residentialFlag) : string
    {
        $returnMessage = "<b>";
        $validToken = 0;
        if(strcmp($apiToken,Configuration::get('flagship_api_token')) != 0 && $this->isTokenValid($apiToken, $testEnv))
        {
            $validToken = Configuration::updateValue('flagship_api_token', $apiToken);
        }

        if($validToken == 1){
            $returnMessage .= "Token Updated! ";
        }

        if($validToken == 0){
            $returnMessage .= "Token not updated! ";
        }

        if($feeFlag){
            $returnMessage .= "Flat Handling Fee updated! ";
        }

        if($markupFlag){
            $returnMessage .= "Percentage Markup Updated! ";
        }

        if($residentialFlag){
            $returnMessage .= "Residential Shipments value updated! ";
        }

        $returnMessage .= "</b>";
        return $returnMessage;
    }

    protected function prepareCarriers($availableServices) : int {
        foreach ($availableServices as $availableService) {
            $carrier = $this->addCarrier($availableService);
            $this->addZones($carrier);
            $this->addGroups($carrier);
            $this->addRanges($carrier);
        }
        return 0;
    }

    protected function getBaseUrl() : string {
        $baseUrl = Configuration::get('flagship_test_env') == 1 ? TEST_API_URL : SMARTSHIP_API_URL;
        return $baseUrl;
    }

    protected function setResidential(string $residential) : int {
        return Configuration::updateValue('flagship_residential', $residential);
    }

    protected function setTestEnv(string $testEnv) : int {
        return Configuration::updateValue('flagship_test_env', $testEnv);
    }

    protected function insertBoxDetails() : string
    {
        $length = Tools::getValue('flagship_box_length');
        $width = Tools::getValue('flagship_box_width');
        $height = Tools::getValue('flagship_box_height');

        $girth = 2*$width + 2*$height;
        if ($this->getWeightUnits() == 'imperial' && ($length + $girth > 165) ||
        ($this->getWeightUnits() == 'metric' &&
        $this->validateMetricDimensions($length, $width, $height) > 165)) {
            return $this->displayWarning($this->l('Box too big'));
        }

        $data = [
            "model" => Tools::getValue('flagship_box_model'),
            "length" => Tools::getValue('flagship_box_length'),
            "width" => Tools::getValue('flagship_box_width'),
            "height" => Tools::getValue('flagship_box_height'),
            "weight" => Tools::getValue('flagship_box_weight'),
            "max_weight" => Tools::getValue('flagship_box_max_weight')
        ];

        Db::getInstance()->insert('flagship_boxes', $data);
        return $this->displayConfirmation($this->l('Box added'));
    }


    protected function validateMetricDimensions(float $length, float $width, float $height) : float
    {
        $length = $length/2.54;
        $width = $width/2.54;
        $height = $height/2.54;

        $girth = 2*$width + 2*$height;

        return $length + $girth;
    }


    protected function verifyToken(string $apiToken, int $testEnv) : bool
    {
        if ($this->isTokenValid($apiToken, $testEnv) && !$this->isCurrentTokenSame($apiToken)) {
            Configuration::updateValue('flagship_api_token', $apiToken);
            return true;
        }
        return false;
    }

    protected function setHandlingFee(string $fee) : int
    {
        return Configuration::updateValue('flagship_fee', $fee);
    }

    protected function setMarkup(string $markup) : int
    {
        return Configuration::updateValue('flagship_markup', $markup);
    }

    protected function isCurrentTokenSame(string $token) : bool
    {
        $currentToken = Configuration::get('flagship_api_token');
        if ($currentToken === $token) {
            return true;
        }
        return false;
    }

    protected function isTokenValid(string $token, int $testEnv) : bool
    {
        $url = $testEnv == 1 ? TEST_API_URL : SMARTSHIP_API_URL;

        $flagship = new Flagship($token, $url, 'Prestashop', _PS_VERSION_); //storeName
        try {
            $storeName = $this->context->shop->name;
            $checkTokenRequest = $flagship->validateTokenRequest($token)->setStoreName($storeName);
            $checkTokenRequest->execute();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    protected function setApiToken(string $apiToken, int $testEnv) : string
    {
        if (!$this->verifyToken($apiToken, $testEnv)) {
            return false;
        }
        Configuration::updateValue('flagship_api_token', $apiToken);
        return true;
    }

    protected function prepareRates(\Flagship\Shipping\Collections\RatesCollection $rates) : array
    {
        $ratesArray = [];
        foreach ($rates as $rate) {
            $ratesArray[] = [
                "courier" => $rate->getCourierName() == 'FedEx' ?
                    'FedEx '.$rate->getCourierDescription() :
                    $rate->getCourierDescription(),
                "subtotal" => $rate->getSubtotal(),
                "taxes" => $rate->getTaxesTotal()
            ];
        }
        return $ratesArray;
    }

    protected function getCourierImage(
        \Flagship\Shipping\Objects\Service $availableService,
        string $courier,
        string $img
    ) : string {
        if (stripos($availableService->getDescription(), $courier) === 0) {
            return Tools::strtolower($courier);
        }
        return $img;
    }

    protected function addCarrier(\Flagship\Shipping\Objects\Service $availableService) //Mixed return type
    {

        $carrier = new Carrier();

        $carrier->name = $this->l($availableService->getDescription());
        $carrier->is_module = true;
        $carrier->active = 1;
        $carrier->range_behavior = 1;
        $carrier->need_range = 1;
        $carrier->shipping_external = true;
        $carrier->range_behavior = 0;
        $carrier->external_module_name = $this->name;
        $carrier->shipping_method = 2;
        $img = 'fedex';

        $couriers = ['canpar','ups','purolator','dhl','dicom'];

        foreach ($couriers as $courier) {
            $img = $this->getCourierImage($availableService, $courier, $img);
        }

        foreach (Language::getLanguages() as $lang) {
            $carrier->delay[$lang['id_lang']] = $this->l('Contact FlagShip');
        }

        if ($carrier->add() == true) {
            @copy(dirname(__FILE__).'/views/img/'.$img.'.png', _PS_SHIP_IMG_DIR_.'/'.(int)$carrier->id.'.jpg');
            Configuration::updateValue($this->name, (int)$carrier->id);

            $this->id_carrier = (int)$carrier->id;
            return $carrier;
        }

        return false;
    }

    protected function addGroups(Carrier $carrier) : int
    {
        $groups_ids = array();
        $groups = Group::getGroups(Context::getContext()->language->id);
        foreach ($groups as $group) {
            $groups_ids[] = $group['id_group'];
        }
        $carrier->setGroups($groups_ids);
        return 0;
    }

    protected function addRanges(Carrier $carrier) : int
    {
        $range_price = new RangePrice();
        $range_price->id_carrier = $carrier->id;
        $range_price->delimiter1 = '0';
        $range_price->delimiter2 = '10000';
        $range_price->add();

        $range_weight = new RangeWeight();
        $range_weight->id_carrier = $carrier->id;
        $range_weight->delimiter1 = '0';
        $range_weight->delimiter2 = '10000';
        $range_weight->add();

        return 0;
    }

    protected function addZones(Carrier $carrier) : int
    {
        $zones = Zone::getZones();
        foreach ($zones as $zone) {
            $carrier->addZone($zone['id_zone']);
        }
        return 0;
    }

    protected function getStateCode(int $code) : string
    {
        if ($code == 0) {
            return 'QC';
        }
        $sql = new DbQuery();
        $sql->select('iso_code');
        $sql->from('state', 's');
        $sql->where('s.id_state = '.$code);

        return Db::getInstance()->executeS($sql)[0]['iso_code'];
    }

    protected function getPayload(Address $address) : array
    {

        $from = [
            "city"=>Configuration::get('PS_SHOP_CITY'),
            "country"=>Country::getIsoById(Configuration::get('PS_SHOP_COUNTRY_ID')),
            "state"=>$this->getStateCode(Configuration::get('PS_SHOP_STATE_ID')),
            "postal_code"=>Configuration::get('PS_SHOP_CODE'),
            "is_commercial"=>true
        ];

        $to = [
            "city"=>$address->city,
            "country"=>Country::getIsoById($address->id_country),
            "state"=>$this->getStateCode($address->id_state),
            "postal_code"=>$address->postcode,
            "is_commercial"=> Configuration::get('flagship_residential') ? false : true
        ];
        $packages = $this->getPackages();

        $payment = [
            "payer" => "F"
        ];
        $options = [
            "address_correction" => true
        ];

        $payload = [
            "from" => $from,
            "to" => $to,
            "packages" => $packages,
            "payment" => $payment,
            "options" => $options
        ];

        return $payload;
    }

    protected function getBoxes() : array
    {
        $query = new DbQuery();
        $query->select('model,length,width,height,weight,max_weight')->from('flagship_boxes');

        $rows = Db::getInstance()->executeS($query);
        $boxes = [];
        foreach ($rows as $row) {
            $boxes[] = [
                "box_model" => $row["model"],
                "length" => $row["length"],
                "width" => $row["width"],
                "height" => $row["height"],
                "weight" => $row["weight"],
                "max_weight" => $row["max_weight"]
            ];
        }
        return $boxes;
    }

    protected function getPackages($order = null) : array
    {
        $products = is_null($order) ? Context::getContext()->cart->getProducts() : $order->getProducts();
        $packages = [];
        $items = [];

        foreach ($products as $product) {
            $items = $this->getItemsByQty($product, $order, $items);
        }

        $boxes = $this->getBoxes();

        if (count($boxes) == 0) {
            for($i=0;$i<count($items);$i++){
                $temp[] = [
                    'length' => 1,
                    'width' => 1,
                    'height' => 1,
                    'weight' => 1,
                    'description' => 'Item '.$i
                ];
            }

            return [
                'items' => $temp,
                "units" => $this->getWeightUnits(),
                "type"  => "package",
                "content" => "goods"
            ];
        }

        $token = Configuration::get('flagship_api_token');
        $url = $this->getBaseUrl();
        $flagship = new Flagship($token, $url, 'Prestashop', _PS_VERSION_);
        $packingPayload = [
            'items' => $items,
            'boxes' => $boxes,
            'units' => $this->getWeightUnits()
        ];

        $packings = $flagship->packingRequest($packingPayload)->execute();
        $packedItems = $this->getPackedItems($packings);

        $packages = [
            "items" => $packedItems,
            "units" => $this->getWeightUnits(),
            "type"  => "package",
            "content" => "goods"
        ];

        return $packages;
    }

    protected function getPackedItems(\Flagship\Shipping\Collections\PackingCollection $packings) : array
    {
        if ($packings == null) {
            return [
                'length' => 1,
                'width' => 1,
                'height' => 1,
                'weight' => 1,
                'description' => 'packed items'
            ];
        }

        foreach ($packings as $packing) {
            $packedItems[] = [
                'length' => $packing->getLength(),
                'width' => $packing->getWidth(),
                'height' => $packing->getHeight(),
                'weight' => $packing->getWeight(),
                'description' => $packing->getBoxModel()
            ];
        }

        return $packedItems;
    }

    protected function getItemsByQty($product, $order, $items) : array
    {
        $qty = is_null($order) ? $product["quantity"] : $product["product_quantity"];

        for ($i=0; $i < $qty; $i++) {
            $items[] = [
                "width"  => $product["width"] == 0 ? 1 : $product["width"],
                "height" => $product["height"] == 0 ? 1 : $product["height"],
                "length" => $product["depth"] == 0 ? 1 : $product["depth"],
                "weight" => $product["weight"] == 0 ? 1 : $product["weight"],
                "description"=>is_null($order) ? $product["name"] : $product["product_name"]
            ];
        }
        return $items;
    }

    protected function updateOrder(int $shipmentId, int $orderId) : bool
    {
        $data = [
            "id_order" => $orderId,
            "flagship_shipment_id" => $shipmentId
        ];
        return Db::getInstance()->insert('flagship_shipping', $data);
    }
}
