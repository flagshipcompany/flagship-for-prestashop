# flagship-for-prestashop

PrestaShop module that allows you to ship with FlagShip.

# Compatibility

Compatible with PrestaShop 1.7.x

# Installation

If you are downloading the module from github, unzip the archive and move the folder to @Prestashop/modules/.

````
unzip flagship-for-prestashop.zip
mv flagship-for-prestashop flagshipshipping
cp -r flagshipshipping <PATH_TO_PRESTASHOP_INSTALLATION_DIR>/modules/
cd <PATH_TO_PRESTASHOP_INSTALLATION_DIR>/modules/flagshipshipping
composer install
````

Login to PrestaShop Admin.

Navigate to Modules > Modules & Services.

Under Selection, search for Flagship and click on install.

# Usage

Click on Configure next to the module. Set the API token

Every order now gives you an option to ship with FlagShip
