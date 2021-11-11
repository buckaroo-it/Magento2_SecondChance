<p align="center">
  <img src="https://www.buckaroo.nl/media/2968/buckaroo.png" width="250px" position="center">
</p>

# Buckaroo Magento 2 Second Chance extension

## Installation
```
composer require buckaroo/magento2secondchance
php bin/magento module:enable Buckaroo_Magento2SecondChance
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
```

## Usage
### General information
The Second Chance module makes it possible to follow up unpaid orders with one or two reminder emails. This extension to the Buckaroo Payment module ensures a higher conversion rate.The Second Chance functionality is fully white-labelled, e-mails can be sent from your own corporate identity and mail servers. On top of that, the module can optionally also take into account whether or not stock is available.

### Requirements
To use the plugin you must use: 
- Magento Open Source version 2.3.x & 2.4.x
- Buckaroo Magento 2 Payment module 1.39.0 or greater 

### Configuration
In the module configuration, various settings are available to build an ideal Second Chance flow to suit everyone. The settings below can be adjusted manually.
* Switching on and off 1st and 2nd email.
* Select template for sending 1st and 2nd email.
* Determine timing for sending 1st and 2nd email.
* Don't send payment reminder when product is out of stock (on/off) 
* Block multiple emails (on/off)

<p align="center">
  <img src="https://www.buckaroo.nl/media/2973/secondchance.png" width="750px" position="center">
</p>

## Contribute
See [Contribution Guidelines](CONTRIBUTING.md)

## Support:

https://support.buckaroo.nl/contact
