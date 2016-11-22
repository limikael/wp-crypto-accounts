<?php

/*
Plugin Name: Crypto Accounts
Plugin URI: http://github.com/limikael/wp-crypto-accounts
GitHub Plugin URI: https://github.com/limikael/wp-crypto-accounts
Description: Let all your users have a bitcoin account.
Version: 0.0.2
*/

require_once __DIR__."/src/plugin/CryptoAccountsPlugin.php";
require_once __DIR__."/src/controller/ShortcodeController.php";
require_once __DIR__."/src/controller/SettingsController.php";
require_once __DIR__."/src/utils/WpUtil.php";
require_once __DIR__."/src/model/Account.php";
require_once __DIR__."/src/model/Transaction.php";
require_once __DIR__."/src/controller/AccountController.php";

use wpblockchainaccounts\WpUtil;
use wpblockchainaccounts\CryptoAccountsPlugin;
use wpblockchainaccounts\ShortcodeController;
use wpblockchainaccounts\SettingsController;
use wpblockchainaccounts\Account;
use wpblockchainaccounts\Transaction;
use wpblockchainaccounts\AccountController;

CryptoAccountsPlugin::instance();
ShortcodeController::instance();
AccountController::instance();

if (is_admin()) {
	SettingsController::instance();
}

// Get a reference to a user account.
if (!function_exists("bca_user_account")) {
	function bca_user_account($user) {
		return Account::getUserAccount($user);
	}
}

// Get a reference to an entity account.
if (!function_exists("bca_entity_account")) {
	function bca_entity_account($entity_type, $entity_id) {
		return Account::getEntityAccount($entity_type, $entity_id);
	}
}

// Make transaction.
if (!function_exists("bca_make_transaction")) {
	function bca_make_transaction($denomination, $fromAccount, $toAccount, $amount, $options=array()) {
		$t=new Transaction();
		$t->setFromAccount($fromAccount);
		$t->setToAccount($toAccount);
		$t->setAmount($denomination,$amount);

		if (isset($options["notice"]))
			$t->setNotice($options["notice"]);

		if (isset($options["confirming"]))
			$t->setUseConfirming($options["confirming"]);

		$t->perform();

		return $t->id;
	}
}

// Get available denominations
if (!function_exists("bca_denominations")) {
	function bca_denominations() {
		return array("bits","mbtc","btc");
	}
}