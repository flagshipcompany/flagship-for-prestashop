# flagship-for-prestashop

PrestaShop module that allows you to ship with FlagShip.

# Compatibility

Compatible with PrestaShop 1.7.x

# Installation

## Composer

````
cd <PATH_TO_PRESTASHOP_INSTALLATION_DIR>/modules
composer create-project flagshipcompany/flagship-for-prestashop FlagshipShipping
````
## Manual
Download the module from github, unzip the archive and move it to @Prestashop/modules/.

````
unzip flagship-for-prestashop.zip
mv flagship-for-prestashop FlagshipShipping
cp -r FlagshipShipping <PATH_TO_PRESTASHOP_INSTALLATION_DIR>/modules/
cd <PATH_TO_PRESTASHOP_INSTALLATION_DIR>/modules/FlagshipShipping
composer install
````

Login to PrestaShop Admin.

Navigate to Modules > Module Catalog

Search for Flagship and click on install.

# Usage

Configure the module. Set API token, markup percentage and handling fee.
![Image of Configuration](https://github.com/flagshipcompany/flagship-for-prestashop/blob/master/views/screenshots/configuration.png)

Customer can select the shipping method.
![Image of Rates](https://github.com/flagshipcompany/flagship-for-prestashop/blob/master/views/screenshots/rates.png)

Admin gets an option to Send orders to FlagShip.
![Image of Order](https://github.com/flagshipcompany/flagship-for-prestashop/blob/master/views/screenshots/order.png)
