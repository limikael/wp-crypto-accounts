# wp-crypto-accounts

This WordPress plugin allows Wordpres users to deposit and withdraw bitcoin.

## User interface

The plugin provides a number of shortcodes that can be used to create the user experience on the frontend of the WordPress site. The shortcodes are:

* `[bca-balance]` - Show the current balance for the currently logged in user.
* `[bca-deposit]` - Show an interface, including a QR-code, for the user to deposit funds into the account.
* `[bca-history]` - Show the account history for the current user.
* `[bca-withdraw]` - Show an interface that lets the current user withdraw funds to an external Bitcoin address.

The look and feel of the user interface is further tweakable using CSS.

## API

There is an API for other plugins to take advangage of the accounting functionality. The functions exposed through this API are:

* `bca_get_user_account($user)` - Return an Account object for the user specified by $user. The $user parameter can be either a user id or a WordPress User object.
* 
