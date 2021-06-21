# Magento 2 Single Sign-On

This Magento 2 extension allows store administrators to log in to their Magento backend account using a SAML compatible Single Sign-on identity provider, such as Auth0, OneLogin or Amazon SSO.

## Installation

The recommended way to install this extension is via composer:

```shell
composer require space48/magento2-sso
```

If you are unable to use composer, you can install the extension manually by downloading the package zip file from the Releases page and extracting it to `app/code/Space48/SSO`.

## Configuration

The extension functionality is disabled by default.

To enable it and configure your Identity Provider, visit the `Stores > Configuration > Advanced > Admin > Single Sign-on` section in the Magento backend.
