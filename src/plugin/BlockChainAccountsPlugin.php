<?php

	require_once __DIR__."/../utils/Singleton.php";
	require_once __DIR__."/../utils/BlockchainWallet.php";
	require_once __DIR__."/../model/Account.php";

	/**
	 * Main plugin class.
	 */
	class BlockChainAccountsPlugin extends Singleton {

		/**
		 * Constructor.
		 */
		public function __construct() {
			$mainFile=WP_PLUGIN_DIR."/wpblockchainaccounts/wpblockchainaccounts.php";

			register_activation_hook($mainFile,array($this,"activate"));
			register_uninstall_hook($mainFile,array("BlockChainAccountsPlugin","uninstall"));
		}

		/**
		 *
		 */
		public function getWallet() {
			if (!$this->wallet) {
				$this->wallet=new BlockchainWallet(
					get_option("blockchainaccounts_wallet_id"),
					get_option("blockchainaccounts_wallet_password")
				);
			}

			return $this->wallet;
		}

		/**
		 * Activate.
		 */
		public function activate() {
			Account::createTable();
		}

		/**
		 * Uninstall.
		 */
		public static function uninstall() {
		}
	}