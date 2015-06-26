<?php

	require_once __DIR__."/../plugin/BlockChainAccountsPlugin.php";
	require_once __DIR__."/../utils/Singleton.php";
	require_once __DIR__."/../utils/Template.php";
	require_once __DIR__."/../model/Account.php";

	/**
	 * Handle shortcodes.
	 */
	class ShortcodeController extends Singleton {

		/**
		 * Constructor.
		 */
		public function __construct() {
			add_shortcode("blockchainaccounts_balance", 
				array($this, "blockchainaccounts_balance"));

			add_shortcode("blockchainaccounts_deposit", 
				array($this, "blockchainaccounts_deposit"));

			wp_register_script("blockchainaccounts-jquery-qrcode",
				plugins_url()."/wpblockchainaccounts/res/jquery.qrcode-0.12.0.min.js");
		}

		/**
		 * Show user balance.
		 */
		public function blockchainaccounts_balance() {
			$account=Account::getCurrentUserAccount();

			if (!$account)
				return "<i>not logged in</i>";

			return $account->getBalance();
		}

		/**
		 * Show deposit address.
		 */
		public function blockchainaccounts_deposit() {
			wp_enqueue_script("blockchainaccounts-jquery-qrcode");

			$account=Account::getCurrentUserAccount();

			if (!$account)
				return "<i>not logged in</i>";

			$address=$account->getDepositAddress();

			$template=new Template(__DIR__."/../template/depositaddress.tpl.php");
			$template->set("depositAddress",$address);
			$template->set("depositLink","bitcoin://".$address);

			return $template->render();
		}
	}