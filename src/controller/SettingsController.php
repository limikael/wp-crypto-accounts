<?php

namespace wpblockchainaccounts;

require_once __DIR__."/../utils/Template.php";
require_once __DIR__."/../plugin/CryptoAccountsPlugin.php";
require_once __DIR__."/../model/Transaction.php";
require_once __DIR__."/../utils/Singleton.php";

use \Exception;

/**
 * Manage the settings page.
 */
class SettingsController extends Singleton {

	/**
	 * Construct.
	 */
	public function __construct() {
		add_action('admin_menu',array($this,'admin_menu'));
	}

	/**
	 * Add options page
	 */
	public function admin_menu() {
		// This page will be under "Settings"
		add_options_page(
			'Crypto Accounts',
			'Crypto Accounts',
			'manage_options', 
			'blockchainaccounts_settings',
			array($this,'create_settings_page')
		);

		add_action('admin_init',array($this,'admin_init'));			
	}		

	/**
	 * Admin init.
	 */
	public function admin_init() {
		wp_enqueue_script("cryptoaccount",
			get_site_url().
			"/wp-content/plugins/wp-crypto-accounts/js/wp-crypto-accounts.js"
		);

		register_setting("blockchainaccounts","blockchainaccounts_block_io_api_key");
		register_setting("blockchainaccounts","blockchainaccounts_wallet_type");
	}

	/**
	 * Create the settings page.
	 */
	public function create_settings_page() {
		$template=new Template(__DIR__."/../template/settings.tpl.php");

		if (isset($_REQUEST["settings-updated"]) && $_REQUEST["settings-updated"]) {
			if (CryptoAccountsPlugin::init()->isSetup()) {
				$wallet=CryptoAccountsPlugin::init()->getWallet();

				try {
					$info=$wallet->setup();
					if ($info)
						$template->set("message",$info);
				}

				catch (Exception $e) {
					$template->set("error","Error initializing wallet: ".$e->getMessage());
				}
			}
		}

		$template->show();
	}
}