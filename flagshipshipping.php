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
define('SMARTSHIP_WEB_URL', 'http://127.0.0.1:3006');
define('SMARTSHIP_API_URL', 'http://127.0.0.1:3002');


class FlagshipShipping extends Module
{

    public function __construct()
    {
        $this->name = 'flagshipshipping';
        $this->tab = 'shipping_logistics';
        $this->version = '1.0.0';
        $this->author = 'FlagShip Courier Solutions';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array(
            'min' => '1.6',
            'max' => _PS_VERSION_
        );
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('FlagShip For PrestaShop');
        $this->description = $this->l('Send your shipments with FlagShip now.');
        $this->description .= $this->l('Drop the hassle of figuring out the best prices.');
        $this->description .= $this->l(' Get real time prices from major courier service providers.');
        $this->description .= $this->l('Your customers will never have to deal with a delayed delivery again.');
        $this->description .= $this->l(' A happy customer is a happy You!');
        $this->registerHook('displayBackOfficeOrderActions');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        if (!Configuration::get('flagshipshipping')) {
            $this->warning = $this->l('No name provided');
        }

    }

    public function install()
    {

        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        $this->addFieldToOrder();

        return parent::install();
    }

    public function uninstall()
    {
        Configuration::deleteByName('flagship_api_token');
        $this->removeFieldFromOrder();
        return parent::uninstall();
    }
    //PrestaShop specific method
    public function getContent()
    {
        $output = null;

        if (Tools::isSubmit('submit'.$this->name)) {
            $apiToken = Tools::getValue('flagship_api_token');

            $output .= $this->setApiToken($apiToken);
        }

        return $output.$this->displayForm();
    }

    public function displayForm()
    {
        $defaultLang = (int)Configuration::get('PS_LANG_DFEAULT');
        $fieldsForm = array();
        $apiTokenStatus = Configuration::get('flagship_api_token') ? 'API Token already set' : 'Set API Token';
        $fieldsForm[0]['form'] = array(
          'legend' => array(
            'title' => $this->l($apiTokenStatus),
          ),
          'input' => array(
            array(
                'type' => 'text',
                'label' => $this->l('Enter Token'),
                'name' => 'flagship_api_token',
                'size' => 50,
                'required' => true
          )
        ),
          'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-primary pull-right'
          )
        );

        $helper = new HelperForm();

        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        // Language
        $helper->default_form_language = $defaultLang;
        $helper->allow_employee_form_lang = $defaultLang;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = false;
        $helper->submit_action = 'submit'.$this->name;
        $helper->toolbar_btn = array(
            'save' => array(
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
                '&token='.Tools::getAdminTokenLite('AdminModules'),
            ),
            'back' => array(
                'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            )
        );


        // Load current value
        $helper->fields_value['flagship_api_token'] = NULL;

        return $helper->generateForm($fieldsForm);
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

    public function getToken()
    {

        if (Configuration::get('flagship_api_token')) {
            return Configuration::get('flagship_api_token');
        }

        echo $this->displayError($this->l('Please set FlagShip API Token'));
        return null;
    }

    public function prepareShipment(string $token, int $orderId)
    {
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

    public function updateShipment(string $token, int $orderId, int $shipmentId)
    {
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

    protected function removeFieldFromOrder() : bool
    {
        $query = 'ALTER TABLE '._DB_PREFIX_.'orders DROP flagship_shipment_id';

        return Db::getInstance()->execute($query);
    }

    protected function setApiToken(string $apiToken) : string
    {

        if (!$this->isTokenValid($apiToken)) {
            return $this->displayError($this->l('Invalid API Token'));
        }
        if ($this->isCurrentTokenSame($apiToken)) {
            return $this->displayWarning($this->l('Same API Token set'));
        }
        Configuration::updateValue('flagship_api_token', $apiToken);
        return $this->displayConfirmation($this->l('Success! API Token saved'));
    }

    protected function getShipmentId(int $id_order)
    {
        $sql = new DbQuery();
        $sql->select('flagship_shipment_id');
        $sql->from('orders', 'o');
        $sql->where('o.id_order = '.$id_order);

        return Db::getInstance()->executeS($sql)[0]['flagship_shipment_id'];
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

    protected function addFieldToOrder() : bool
    {
        $query = 'ALTER TABLE '._DB_PREFIX_.'orders ADD flagship_shipment_id varchar(15) default NULL';

        return Db::getInstance()->execute($query);
    }

    protected function createPayload(int $orderId) : array
    {

        $from = array(
            "name"=>Configuration::get('PS_SHOP_NAME'),
            "attn"=>Configuration::get('PS_SHOP_NAME'),
            "address"=>Configuration::get('PS_SHOP_ADDR1'),
            "suite"=>Configuration::get('PS_SHOP_ADDR2'),
            "city"=>Configuration::get('PS_SHOP_CITY'),
            "country"=>$this->getCountryCode(Configuration::get('PS_SHOP_COUNTRY_ID')),
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
            "country"=>$this->getCountryCode((int)$addressTo->id_country),
            "state"=>$this->getStateCode((int)$addressTo->id_state),
            "postal_code"=>$addressTo->postcode,
            "phone"=> $addressTo->phone,
            "is_commercial"=>"false"
        );

        $package = array(
            "units" => $this->getWeightUnits(),
            "type" => "package",
            "items" => array(
                array(
                    "width"=>"1",
                    "height"=>"1",
                    "length"=>"1",
                    "weight"=>$this->getTotalWeight($products)
                )
            )
        );

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

    protected function getCountryCode(int $code) : string
    {
        $sql = new DbQuery();
        $sql->select('iso_code');
        $sql->from('country', 'c');
        $sql->where('c.id_country = '.$code);
        return Db::getInstance()->executeS($sql)[0]['iso_code'];
    }

    protected function getStateCode(int $code) : string
    {
        $sql = new DbQuery();
        $sql->select('iso_code');
        $sql->from('state', 's');
        $sql->where('s.id_state = '.$code);
        return Db::getInstance()->executeS($sql)[0]['iso_code'];
    }

    protected function updateOrder(int $shipmentId, int $orderId) : bool
    {
        $update = 'UPDATE '._DB_PREFIX_.'orders SET `flagship_shipment_id`='.$shipmentId.' where id_order='.$orderId;
        return Db::getInstance()->execute($update);
    }
}
