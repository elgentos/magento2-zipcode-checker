# zipcode-checker

## Prepare the project for the Dutch market.

The layout of the checkout in a default Magento environment differs from the usual Dutch layout. For example, you want to have the address field split into separate fields such as street and house number. This is also necessary for the postcode check. The command `php bin/magento elgentos:checkout:setup-dutch-fields` changes the settings of the Hyva Checkout to the usual layout for the Dutch market. Be careful, it modifies your existing configuration.
