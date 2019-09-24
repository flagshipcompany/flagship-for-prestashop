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

require_once 'vendor/autoload.php';

use Flagship\Shipping\Flagship;

if (!defined('_PS_VERSION_')) {
    exit;
}

//NO Tailing slashes please
define('SMARTSHIP_WEB_URL', 'https://smartship-ng.flagshipcompany.com');
define('SMARTSHIP_API_URL', 'https://api.smartship.io');

class FlagshipShipping extends CarrierModule
{
    protected $config_form = false;
    public $id_carrier;

    public function __construct()
    {
        $this->name = 'FlagshipShipping';
        $this->tab = 'shipping_logistics';
        $this->version = '1.0.0';
        $this->author = 'FlagShip Courier Solutions';
        $this->need_instance = 0;

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

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);

         $this->registerHook('displayBackOfficeOrderActions');
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */

    public function install()
    {
        if (extension_loaded('curl') == false)
        {
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

        return parent::install() ;
    }

    public function uninstall()
    {
        Configuration::deleteByName('flagship_api_token');
        Configuration::deleteByName('flagship_fee');
        Configuration::deleteByName('flagship_markup');

        Db::getInstance()->execute('DROP TABLE '._DB_PREFIX_.'flagship_shipping');
        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        $output = '';
        if (((bool)Tools::isSubmit('submit'.$this->name.'Module')) == true) {
            $output .= $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        return $output.$this->renderForm();
    }

    public function hookDisplayBackOfficeOrderActions(array $params)
    {
        $id_order = $params["id_order"];

        $shipmentId = $this->getShipmentId($id_order);
        $shipmentFlag = is_null($shipmentId) ? 0 : $shipmentId;
        $this->context->smarty->assign(array(
            'shipmentFlag' => $shipmentFlag,
            'SMARTSHIP_WEB_URL' => SMARTSHIP_WEB_URL,
            'orderId' => $id_order,
            'img_dir' => _PS_IMG_DIR_,
        ));
        return $this->display(__FILE__, 'flagship.tpl');
    }

    public function prepareShipment($token,$orderId){
         try {
            $flagship = new Flagship($token, SMARTSHIP_API_URL,'Prestashop',_PS_VERSION_);
            $prepareShipment = $flagship->prepareShipmentRequest($this->createPayload($orderId));
            $prepareShipment = $prepareShipment->execute();
            $shipmentId = $prepareShipment->shipment->id;
            $this->updateOrder($shipmentId, $orderId);
            $link = "<a href='".SMARTSHIP_WEB_URL."/shipping/".$shipmentId."/convert'target='_blank'>";
            $link .= $shipmentId."</a>";
            return $this->displayConfirmation('FlagShip Shipment Id: '.$link);
        } catch (Exception $e) {
            return $this->displayError($e->getMessage());
        }
    }

    public function updateShipment(string $token,int $orderId, int $shipmentId){
        try {
            $flagship = new Flagship($token, SMARTSHIP_API_URL,'Prestashop',_PS_VERSION_);
            $updateShipment = $flagship->editShipmentRequest($this->createPayload($orderId), $shipmentId);
            $updatedShipment = $updateShipment->execute();
            $updatedShipmentId = $updatedShipment->shipment->id;
            $link = "<a href='".SMARTSHIP_WEB_URL."/shipping/".$updatedShipmentId."/convert'target='_blank'>";
            $link .= $updatedShipmentId."</a>";
            return $this->displayConfirmation('Updated! FlagShip Shipment Id: '.$link);
        } catch (Exception $e) {
            return $this->displayError($e->getMessage());
        }
    }

    public function getOrderShippingCost($params, $shipping_cost) //do not use return type
    {
        $id_address_delivery = Context::getContext()->cart->id_address_delivery;
        $address = new Address($id_address_delivery);

        if($id_address_delivery == 0){
            return $shipping_cost;
        }

        $carrier = new Carrier($this->id_carrier);
        if(isset(Context::getContext()->cookie->rates)){

            $couriers = [];
            $rate = explode(",",Context::getContext()->cookie->rate);
            foreach ($rate as $value) {
                $couriers[] = substr($value,0,strpos($value,"-"));
            }
            if(!in_array($carrier->name,$couriers)) return false;
            foreach ($rate as $value) {
                $cost = floatVal(substr($value,strpos($value,"-")+1));
                $cost += floatVal((Configuration::get("flagship_markup")/100) * $cost);
                $cost += floatVal(Configuration::get('flagship_fee'));

                $shipping_cost = substr($value,0,strpos($value,"-")) == $carrier->name ? $cost : $shipping_cost;
            }
            return $shipping_cost;
        }

        $token = Configuration::get('flagship_api_token');
        $flagship = new Flagship($token, SMARTSHIP_API_URL,'Prestashop',_PS_VERSION_);
        $payload = $this->getPayload($address);

        if(!isset(Context::getContext()->cookie->rates)){
            $rates = $flagship->createQuoteRequest($payload)->execute()->sortByPrice();
            Context::getContext()->cookie->rates = 1;

            $ratesArray = $this->prepareRates($rates);

            $str = '';
            foreach ($ratesArray as  $value) {
                $str .= implode("-",$value).",";
            }
            $str = rtrim($str);
            Context::getContext()->cookie->rate = $str;
        }
        return $shipping_cost;
    }

    public function getOrderShippingCostExternal($params) : bool
    {
        return true;
    }

    public function hookActionValidateCustomerAddressForm(){
        unset(Context::getContext()->cookie->rates);
        unset(Context::getContext()->cookie->rate);
    }

    protected function getShipmentId(int $id_order)
    {
        $sql = new DbQuery();
        $sql->select('flagship_shipment_id');
        $sql->from('flagship_shipping', 'fs');
        $sql->where('fs.id_order = '.$id_order);
        $shipmentId = Db::getInstance()->executeS($sql);
        if(empty($shipmentId)){
            return NULL;
        }
        return $shipmentId[0]['flagship_shipment_id'];
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitFlagshipShippingModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    protected function createPayload(int $orderId) : array
    {
        $from = array(
            "name"=>Configuration::get('PS_SHOP_NAME'),
            "attn"=>Configuration::get('PS_SHOP_NAME'),
            "address"=>Configuration::get('PS_SHOP_ADDR1'),
            "suite"=>Configuration::get('PS_SHOP_ADDR2'),
            "city"=>Configuration::get('PS_SHOP_CITY'),
            "country"=>Country::getIsoById(Configuration::get('PS_SHOP_COUNTRY_ID')),
            "state"=>$this->getStateCode(Configuration::get('PS_SHOP_STATE_ID')),
            "postal_code"=>Configuration::get('PS_SHOP_CODE'),
            "phone"=> Configuration::get('PS_SHOP_PHONE'),
            "is_commercial"=>"true"
        );
        $order = new Order($orderId);
        $addressTo = new Address($order->id_address_delivery);
        $products = $order->getProducts();
        $name = !($addressTo->company) ? $addressTo->firstname : $addressTo->company;
        $to = array(
            "name"=>$name,
            "attn"=>$addressTo->firstname,
            "address"=>$addressTo->address1,
            "suite"=>$addressTo->address2,
            "city"=>$addressTo->city,
            "country"=>Country::getIsoById((int)$addressTo->id_country),
            "state"=>$this->getStateCode((int)$addressTo->id_state),
            "postal_code"=>$addressTo->postcode,
            "phone"=> $addressTo->phone,
            "is_commercial"=>"false"
        );
        $package = $this->getPackages($order);
        $options = array(
            "signature_required"=>false,
            "reference"=>"PrestaShop Order# ".$orderId
        );
        $payment = array(
            "payer"=>"F"
        );
        $payload = array(
            'from' => $from,
            'to'  => $to,
            'packages' => $package,
            'options' => $options,
            'payment' => $payment
        );
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
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    [
                        'col' => 4,
                        'type' => 'text',
                        'desc' => Configuration::get('flagship_api_token') ? 'API Token is set' : $this->l('Enter API Token'),
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
                    ]

                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues() : array
    {
        $apiToken = Configuration::get('flagship_api_token') ? Configuration::get('flagship_api_token') : '';
        return array(
            'flagship_api_token' => '',
            'flagship_markup' => Configuration::get('flagship_markup'),
            'flagship_fee' => Configuration::get('flagship_fee')
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess() : string
    {
        $apiToken = empty(Tools::getValue('flagship_api_token')) ? Configuration::get('flagship_api_token') : Tools::getValue('flagship_api_token');
        $fee = empty(Tools::getValue('flagship_fee')) ? 0 : Tools::getValue('flagship_fee');
        $markup = empty(Tools::getValue('flagship_markup')) ? 0 : Tools::getValue('flagship_markup');

        if(is_string(Configuration::get('flagship_fee')) && is_string(Configuration::get('flagship_api_token')) && is_string(Configuration::get('flagship_markup'))){ //fields exist in db

            $feeFlag = $fee != Configuration::get('flagship_fee') ? Configuration::updateValue('flagship_fee',$fee) : 0 ;
            $markupFlag = $markup != Configuration::get('flagship_markup') ? Configuration::updateValue('flagship_markup',$markup) : 0 ;

            $returnFlag = $apiToken != Configuration::get('flagship_api_token') ? ($this->isTokenValid($apiToken) ? Configuration::updateValue('flagship_api_token',$apiToken) : 0) : 0;

            $returnValue = $returnFlag || ($feeFlag || $markupFlag) ? $this->displayConfirmation($this->l('Configuration Updated')) :  $this->displayWarning($this->l("Configuration unchanged"));
            return $returnValue;
        }

        if($this->setApiToken($apiToken) && $this->setMarkup($markup) && $this->setHandlingFee($fee)){

            $flagship = new Flagship($apiToken, SMARTSHIP_API_URL,'Prestashop',_PS_VERSION_);

            $availableServices = $flagship->availableServicesRequest()->execute();

            foreach ($availableServices as $availableService) {
                $carrier = $this->addCarrier($availableService);
                $this->addZones($carrier);
                $this->addGroups($carrier);
                $this->addRanges($carrier);
            }
            return $this->displayConfirmation($this->l('FlagShip Configured'));
        }

        return $this->displayWarning($this->l("Oops! Token is invalid or same token is set."));

    }

    protected function verifyToken($apiToken) : bool {

        if($this->isTokenValid($apiToken) && !$this->isCurrentTokenSame($apiToken)){
            Configuration::updateValue('flagship_api_token', $apiToken);
            return true;
        }
        return false;
    }

    protected function setHandlingFee($fee) : int {
        return Configuration::updateValue('flagship_fee', $fee);
    }

    protected function setMarkup($markup) : int {
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

    protected function isTokenValid(string $token) : bool
    {
        $flagship = new Flagship($token, SMARTSHIP_API_URL,'Prestashop',_PS_VERSION_);
        try {
            $checkTokenRequest = $flagship->validateTokenRequest($token);
            $checkTokenRequest->execute();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    protected function setApiToken(string $apiToken) : string
    {
        if(!$this->verifyToken($apiToken)){
            return false;
        }
        Configuration::updateValue('flagship_api_token', $apiToken);
        return true;
    }

    protected function prepareRates($rates) : array {
        $ratesArray = [];
        foreach ($rates as $rate) {
            $ratesArray[] = [
                "courier" => $rate->getCourierName() == 'FedEx' ? 'FedEx '.$rate->getCourierDescription() : $rate->getCourierDescription(),
                "subtotal" => $rate->getTotal()
            ];
        }
        return $ratesArray;
    }

    protected function getCourierImage($availableService, string $courier,string $img){
        if(stripos($availableService->getDescription(),$courier) === 0){
            return strtolower($courier);
        }
        return $img;
    }

    protected function addCarrier($availableService)
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

        $couriers = ['canpar','ups','purolator','dhl'];

        foreach ($couriers as $courier) {
            $img = $this->getCourierImage($availableService,$courier,$img);
        }

        foreach (Language::getLanguages() as $lang)
            $carrier->delay[$lang['id_lang']] = $this->l('Contact FlagShip');

        if ($carrier->add() == true)
        {
            @copy(dirname(__FILE__).'/views/img/'.$img.'.png', _PS_SHIP_IMG_DIR_.'/'.(int)$carrier->id.'.jpg');
            Configuration::updateValue($this->name, (int)$carrier->id);

            $this->id_carrier = (int)$carrier->id;
            return $carrier;
        }

        return false;
    }

    protected function addGroups($carrier)
    {
        $groups_ids = array();
        $groups = Group::getGroups(Context::getContext()->language->id);
        foreach ($groups as $group)
            $groups_ids[] = $group['id_group'];

        $carrier->setGroups($groups_ids);
    }

    protected function addRanges($carrier)
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
    }

    protected function addZones($carrier)
    {
        $zones = Zone::getZones();

        foreach ($zones as $zone)
            $carrier->addZone($zone['id_zone']);
    }

    protected function getStateCode(int $code) : string
    {
        if($code == 0){
            return 'QC';
        }
        $sql = new DbQuery();
        $sql->select('iso_code');
        $sql->from('state', 's');
        $sql->where('s.id_state = '.$code);

        return Db::getInstance()->executeS($sql)[0]['iso_code'];
    }

    protected function getPayload($address) : array {

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
            "is_commercial"=>true
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

    protected function getPackages($order = null) : array {
        $products = is_null($order) ? Context::getContext()->cart->getProducts() : $order->getProducts();
        $packages = [];
        $items = [];

        foreach ($products as $product) {

            $items[] = [
                "width"  => $product["width"] == 0 ? 1 : $product["width"],
                "height" => $product["height"] == 0 ? 1 : $product["height"],
                "length" => $product["depth"] == 0 ? 1 : $product["depth"],
                "weight" => $product["weight"] == 0 ? 1 : $product["weight"],
                "description"=>is_null($order) ? $product["name"] : $product["product_name"]
            ];
        }
        $packages = [
            "items" => $items,
            "units" => "imperial",
            "type"  => "package",
            "content" => "goods"
        ];

        return $packages;
    }

    protected function updateOrder(int $shipmentId, int $orderId) : bool
    {
        $update = 'INSERT INTO `'._DB_PREFIX_.'flagship_shipping`(`id_order`,`flagship_shipment_id`) values('.$orderId.','.$shipmentId.')';
        return Db::getInstance()->execute($update);
    }
}
