<?php

	namespace wpblockchainaccounts;

	require_once __DIR__."/../utils/Template.php";
	require_once __DIR__."/../plugin/BlockChainAccountsPlugin.php";
	require_once __DIR__."/../utils/Singleton.php";

	/**
	 * Manage the settings page.
	 */
	class SettingsController extends Singleton {

		/**
		 * Construct.
		 */
		public function __construct() {
			$this->settings=array(array(
				"setting"=>"blockchainaccounts_wallet_id",
				"title"=>"Wallet id",
				"description"=>"You wallet id at blockchain.info"
			), array(
				"setting"=>"blockchainaccounts_wallet_password",
				"title"=>"Wallet password",
				"description"=>"Your password at blockchain.info"
			), array(
				"setting"=>"blockchainaccounts_notification_key",
				"title"=>"Notifications key",
				"description"=>"This key is used to identify incoming notifications"
			));

			add_action('admin_menu',array($this,'admin_menu'));
		}

		/**
		 * Add options page
		 */
		public function admin_menu() {
			// This page will be under "Settings"
			add_options_page(
				'Blockchain Accounts',
				'Blockchain Accounts',
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
			foreach ($this->settings as $setting) {
				register_setting("blockchainaccounts",$setting["setting"]);
			}
		}

		/**
		 * Create the settings page.
		 */
		public function create_settings_page() {
			$notificationUrl=
				plugins_url().
				"/wpblockchainaccounts/notification.php?key=".
				get_option("blockchainaccounts_notification_key");

			$wpcaUrl=
				plugins_url().
				"/wpblockchainaccounts/api.php";

			$template=new Template(__DIR__."/../template/settings.tpl.php");
			$template->set("settings",$this->settings);
			$template->set("notificationUrl",$notificationUrl);
			$template->set("wpcaUrl",$wpcaUrl);
			$template->show();
		}
	}