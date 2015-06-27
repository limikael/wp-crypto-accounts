<?php

	require_once __DIR__."/../utils/Singleton.php";
	require_once __DIR__."/../utils/BlockchainWallet.php";
	require_once __DIR__."/../model/Account.php";
	require_once __DIR__."/../model/Transaction.php";

	/**
	 * Main plugin class.
	 */
	class BlockChainAccountsPlugin extends Singleton {

		private $optionDefaults;
		private $wallet;

		/**
		 * Constructor.
		 */
		public function __construct() {
			$mainFile=WP_PLUGIN_DIR."/wpblockchainaccounts/wpblockchainaccounts.php";

			register_activation_hook($mainFile,array($this,"activate"));
			register_uninstall_hook($mainFile,array("BlockChainAccountsPlugin","uninstall"));

			$this->optionDefaults=array(
				"blockchainaccounts_wallet_id"=>NULL,
				"blockchainaccounts_wallet_password"=>NULL,
				"blockchainaccounts_notification_key"=>md5(rand().microtime()),
				"blockchainaccounts_notifications"=>2,
			);

			/*error_reporting(E_ALL);
			ini_set('display_errors', 1);*/
		}

		/**
		 * Get blockchain.info wallet.
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
			Account::install();
			Transaction::install();

			foreach ($this->optionDefaults as $option=>$default)
				if (!get_option($option))
					update_option($option,$default);
		}

		/**
		 * Uninstall.
		 */
		public static function uninstall() {
			Account::uninstall();
			Transaction::uninstall();
		}
	}