<p align="center">
  <a href="https://www.buckaroo.nl">
    <img src="https://raw.githubusercontent.com/buckaroo-it/Media/main/Buckaroo/README.md%20Headers/buckaroo-magento2-second-chance-header-rounded.png" alt="Buckaroo — Second Chance for Magento 2" width="100%">
  </a>
</p>

<h1 align="center">Buckaroo Second Chance for Magento 2</h1>

---

> [!WARNING]
> **Second Chance is now part of the Buckaroo Magento 2 plugin.** This separate module is no longer needed and is no longer developed. Do not install `buckaroo/magento2secondchance` in a new project — install the [Buckaroo Magento 2 plugin](https://github.com/buckaroo-it/Magento2), which includes Second Chance out of the box.

---

## About

Second Chance follows up unpaid orders with one or two reminder e-mails, which recovers orders that would otherwise be lost and raises your conversion rate. The e-mails are fully white-labelled, so they go out in your own corporate identity.

The functionality used to live in this repository as a separate module. It has since been merged into the main [Buckaroo Magento 2 plugin](https://github.com/buckaroo-it/Magento2), which now declares `replaces: buckaroo/magento2secondchance` in its `composer.json`.

This repository is kept online for reference and for merchants who are still on an older setup.

---

## Migrating to the main plugin

If you currently have the separate module installed, remove it and make sure you are on a plugin version that includes Second Chance. Run the following from your Magento 2 root folder:

```bash
composer remove buckaroo/magento2secondchance
composer update buckaroo/magento2
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy
php bin/magento cache:flush
```

> [!IMPORTANT]
> Test this on a staging environment first and check your Second Chance settings and e-mail templates afterwards. Verify that reminder e-mails still go out as expected before you rely on it in production.

---

## Configuration

Second Chance is configured in the main plugin, under **Stores → Configuration → Sales → Buckaroo** in the Magento admin. The available settings are:

- Switch the first and second reminder e-mail on or off.
- Choose the e-mail template for each reminder.
- Set the timing for each reminder.
- Choose whether to send a reminder when the product is out of stock.

Step-by-step instructions: [Second Chance documentation](https://docs.buckaroo.io/docs/magento-2-new-additional-modules-second-chance-module)

---

## Support

Questions about Second Chance belong with the main plugin, since that is where the code now lives.

- **Bug reports and feature requests:** [open an issue on the main plugin](https://github.com/buckaroo-it/Magento2/issues)
- **Technical support:** [support@buckaroo.nl](mailto:support@buckaroo.nl)
- **Phone:** +31 (0)30 711 50 50
- **Gateway status:** [status.buckaroo.io](https://status.buckaroo.io/)

---

## License

This module is open source software licensed under the [MIT license](https://github.com/buckaroo-it/Magento2_SecondChance/blob/master/LICENSE).

---

<p align="center">
  <sub>Made with care by <a href="https://www.buckaroo.nl">Buckaroo</a>.<br>
  This document is subject to change; typos and language errors are possible.</sub>
</p>
