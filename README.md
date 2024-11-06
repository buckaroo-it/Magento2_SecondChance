<p align="center">
  <img src="https://github.com/user-attachments/assets/8da148e4-63ec-410b-af0e-033a8ad2aa9e" width="200px" position="center">
</p>

# Buckaroo Magento 2 Second Chance plugin
[![Latest release](https://badgen.net/github/release/buckaroo-it/Magento2_SecondChance)](https://github.com/buckaroo-it/Magento2_SecondChance/releases)

### Index
- [About](#about)
- [Installation](#installation)
- [Requirements](#requirements)
- [Configuration](#configuration)
- [Contribute](#contribute)
- [Versioning](#versioning)
- [Additional information](#additional-information)
---

### About
The Second Chance module makes it possible to follow up unpaid orders with one or two reminder emails. This extension to the Buckaroo Payment module ensures a higher conversion rate.The Second Chance functionality is fully white-labelled, e-mails can be sent from your own corporate identity and mail servers. On top of that, the module can optionally also take into account whether or not stock is available.

### Installation
```
composer require buckaroo/magento2secondchance
php bin/magento module:enable Buckaroo_Magento2SecondChance
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
```

### Requirements

**To use the plugin you must use:**
- Magento Open Source version 2.4.5, 2.4.6, and 2.4.7
- Buckaroo Magento 2 Payments plugin 1.50.2 or higher.

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

### Contribute

We really appreciate it when developers contribute to improve the Buckaroo plugins.
If you want to contribute as well, then please follow our [Contribution Guidelines](CONTRIBUTING.md).

### Versioning

<p align="left">
  <img src="https://www.buckaroo.nl/media/3480/magento_versioning.png" width="500px" position="center">
</p>

- **MAJOR:** Breaking changes that require additional testing/caution.
- **MINOR:** Changes that should not have a big impact.
- **PATCHES:** Bug and hotfixes only.

### Additional information
- **Support:** https://support.buckaroo.eu/contact
- **Contact:** [support@buckaroo.nl](mailto:support@buckaroo.nl) or [+31 (0)30 711 50 50](tel:+310307115050)

<b>Please note:</b><br>
This file has been prepared with the greatest possible care and is subject to language and/or spelling errors.
