# flagship-for-prestashop

PrestaShop module that allows you to ship with FlagShip. We strongly recommend you to use the latest version of PrestaShop.


NOTE: FlagShip for Prestashop requires you to increase PHP upload_max_filesize to at least 3M. This can be done in your php.ini. Based on your server, the location of thsi file may vary. For apache, the file can be found at /etc/php/apache2/php.ini. For nginx, it is at /etc/php/7.1/fpm/php.ini

# Requirements

We recommend using the latest version of PHP. The minimum requirement is PHP 7.1

# Compatibility

Compatible with PrestaShop 1.7.x

# Installation

## Module Upload

Login to PrestaShop Admin

Modules > Module Manager > Upload a module (top right)

Upload flagship-for-prestashop.zip provided above

## Composer

````
cd <PATH_TO_PRESTASHOP_INSTALLATION_DIR>/modules
composer create-project flagshipcompany/flagship-for-prestashop flagshipshipping
````
## Manual
Download the module from github, unzip the archive and move it to @Prestashop/modules/.

````
unzip flagship-for-prestashop.zip
mv flagship-for-prestashop flagshipshipping
cp -r flagshipshipping <PATH_TO_PRESTASHOP_INSTALLATION_DIR>/modules/
cd <PATH_TO_PRESTASHOP_INSTALLATION_DIR>/modules/flagshipshipping
composer install
````

Login to PrestaShop Admin.

Navigate to Modules > Module Catalog

Search for Flagship and click on install.

# Usage

Make sure store address is set. To set this,

Login to PrestaShop Admin > Configure > Shop Parameters > Contact > Select Stores Tab > Scroll down to Contact details and save address.

![Image of Contact Details](https://github.com/flagshipcompany/flagship-for-prestashop/blob/master/views/img/contact.png)

Configure the module. Set API token, markup percentage and handling fee. Add dimensions for shipping boxes here.

![Image of Configuration](https://github.com/flagshipcompany/flagship-for-prestashop/blob/master/views/img/configuration.png)

Customer can select the shipping method.

![Image of Rates](https://github.com/flagshipcompany/flagship-for-prestashop/blob/master/views/img/rates.png)

Admin gets an option to Send orders to FlagShip.

![Image of Order](https://github.com/flagshipcompany/flagship-for-prestashop/blob/master/views/img/order.png)

To change the transit time for a carrier
![Image of Edit Carrier](https://github.com/flagshipcompany/flagship-for-prestashop/blob/master/views/img/editCarrier.jpg)

![Image of Transit Time](https://github.com/flagshipcompany/flagship-for-prestashop/blob/master/views/img/editCarrierTransitTime.jpg)

![Image of Transit Time Changed](https://github.com/flagshipcompany/flagship-for-prestashop/blob/master/views/img/editCarrierTransitTimeChanged.jpg)
