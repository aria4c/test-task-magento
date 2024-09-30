
# Vendor_Test Module

## Overview

Magento 2 test task.
- *cart* CustomerData section is used to store message
- *extraInfo* block in minicart is used to keep compatibility with third-party extensions
- different templates for cart page and minicart is used to allow better Frontend customization

## Features

- Enable or disable the product dependency feature.
- Add 'has_dependency' attribute
- Prevents checkout when product with dependency is in the cart
- Add notice message in minicart and cart when enabled
- Provides a CLI command to automatically set dependencies and provide links to products.

## Installation

Follow the steps below to install the module:

1. Upload the module to `app/code/Vendor/Test`.
2. Run the following commands to install the module:

    ```bash
    php bin/magento setup:upgrade
    php bin/magento setup:di:compile
    php bin/magento setup:static-content:deploy
    php bin/magento cache:flush
    ```

3. Log in to the Magento Admin Panel and navigate to **Stores > Configuration > Catalog > Product Dependency** to configure the module.

## Configuration

Once the module is installed, you can configure it under **Stores > Configuration > Catalog > Product Dependency**.

### Available Configuration Options:

- **Enabled**:  
  Enable or disable the product dependency functionality.
- **Product ID**:  
  Set the ID of the dependent product.

### How to Configure:

1. Navigate to **Stores > Configuration > Catalog > Product Dependency** in the Magento Admin Panel.
## CLI Command
The module provides a CLI command that allows you to automatically set the `has_dependency` for sample data product with SKU 24-MB01. Default dependent product is product with ID 2. 
Command works only if sample data was installed. 

```bash
php bin/magento vendor:test:setup
