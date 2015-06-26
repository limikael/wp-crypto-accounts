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

	ActiveRecord::setTablePrefix(WpUtil::getTablePrefix());
	ActiveRecord::setPdo(WpUtil::getCompatiblePdo());

	BlockChainAccountsPlugin::init();
	ShortcodeController::init();

	if (is_admin()) {
		SettingsController::init();
	}
