# wp-crypto-accounts

This WordPress plugin allows Wordpres users to deposit and withdraw bitcoin.

It makes a WordPress instance function as a bank, and can be used as a foundation to create banking and financial software in WordPress.

## User interface

The plugin provides a number of shortcodes that can be used to create the user experience on the frontend of the WordPress site. The shortcodes are:

* `[bca-balance]` - Show the current balance for the currently logged in user.
* `[bca-deposit]` - Show an interface, including a QR-code, for the user to deposit funds into the account.
* `[bca-history]` - Show the account history for the current user.
* `[bca-withdraw]` - Show an interface that lets the current user withdraw funds to an external Bitcoin address.

The look and feel of the user interface is further tweakable using CSS.

## API

There is an API for other plugins to take advangage of the accounting functionality. The functions exposed through this API are:

* `bca_get_user_account($user)`<br>Get a reference to an Account object for the user specified by $user. The $user parameter can be either a user id or a WordPress User object.
* `bca_entity_account($entity_type, $entity_id)`<br>Get a reference to an Account object for the specified entity type, with the specified entity id. There is no fixed set of entity types, and the $entity_type is just a plain string. 
* `bca_make_transaction($denomination, $fromAccount, $toAccount, $amount, $options)`<br>Move the specified amount from the fromAccount to the toAccount. The accounts should be specified using Account objects returned by the functions above. The $denomination parameter is a string, and should ba any of `btc`, `mbtc` or `satoshi`. The $options parameter is optional, and may contain the following fields:
  * `notice` - Specify the text to appear next to the transation in the account history.
  * `confirming` - If this is set to true, it allows moving funds from accounts that have unconfirmed transactions. Use with caution!

## Security

So, if the WordPress site is hacked, all the Bitcoins will be stolen, right? Actually not, since no private keys is stored on the system. As a user withdraws funds, the Bitcoins will not be moved directly. Rather, a transaction will be stored in the database. There is then a REST api that can be used to actually perform the transactions, and the REST api takes the private key as input.
