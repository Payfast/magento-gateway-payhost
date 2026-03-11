# magento-gateway-payhost

## Payfast Gateway (PayHost) module v1.2.0 for Magento v2.4.8

This is the Payfast Gateway (PayHost) module for Magento 2. Please feel free
to [contact the Payfast support team](https://payfast.io/contact/) should you require any assistance.

## Installation

1. **Download the Plugin**

    - Visit the [releases page](https://github.com/Payfast/magento-gateway-payhost/releases) and
      download [PayGate.zip](https://github.com/Payfast/magento-gateway-payhost/releases/download/v1.2.0/PayGate.zip).

2. **Install the Plugin**

    - Extract the contents of `PayGate.zip`, then upload the newly created **PayGate** directory into your Magento
      app/code directory (e.g. magentorootfolder/app/code/).
    - Run the following Magento CLI commands:
        ```console
        php bin/magento module:enable PayGate_PayHost
        php bin/magento setup:upgrade
        php bin/magento setup:di:compile
        php bin/magento setup:static-content:deploy
        php bin/magento indexer:reindex
        php bin/magento cache:clean
        ```
3. **Configure the Plugin**

    - Login to the Magento admin panel.
    - Navigate to **Stores > Configuration > Sales > Payment Methods** and click on
      **Payfast Gateway (PayHost)**.
    - Configure the module according to your needs, then click the **Save Config** button.

## Collaboration

Please submit pull requests with any tweaks, features or fixes you would like to share.
