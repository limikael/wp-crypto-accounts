<?php

/*
Plugin Name: BLockchain Accounts
Plugin URI: http://github.com/limikael/wpblockchainaccounts
Version: 0.0.1
*/

	require_once __DIR__."/src/plugin/BlockChainAccountsPlugin.php";
	require_once __DIR__."/src/controller/ShortcodeController.php";
	require_once __DIR__."/src/controller/SettingsController.php";
	require_once __DIR__."/src/utils/WpUtil.php";

	use wpblockchainaccounts\WpUtil;
	use wpblockchainaccounts\BlockChainAccountsPlugin;
	use wpblockchainaccounts\ShortcodeController;
	use wpblockchainaccounts\SettingsController;

	BlockChainAccountsPlugin::init();
	ShortcodeController::init();

	if (is_admin()) {
		SettingsController::init();
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
		function bca_make_transaction($denomination, $fromAccount, $toAccount, $amount, $message=NULL) {
			$t=new Transaction();
			$t->fromAccount=$fromAccount;
			$t->toAccount=$toAccount;
			$t->setAmount($denomination,$amount);
			$t->perform();

			return $t->id;
		}
	}