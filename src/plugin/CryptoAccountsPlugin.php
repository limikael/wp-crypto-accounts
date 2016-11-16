<?php

namespace wpblockchainaccounts;

require_once __DIR__."/../utils/Singleton.php";
require_once __DIR__."/../wallet/BlockchainWallet.php";
require_once __DIR__."/../wallet/BlockIoWallet.php";
require_once __DIR__."/../wallet/MockWallet.php";
require_once __DIR__."/../model/Account.php";
require_once __DIR__."/../model/Transaction.php";

use \Exception;

/**
 * Main plugin class.
 */
class CryptoAccountsPlugin extends Singleton {

	private $optionDefaults;
	private $wallet;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$mainFile=WP_PLUGIN_DIR."/wp-crypto-accounts/wp-crypto-accounts.php";

		register_activation_hook($mainFile,array($this,"activate"));
		register_uninstall_hook($mainFile,array("wp-crypto-accounts\\CryptoAccountsPlugin","uninstall"));

		$this->optionDefaults=array(
			"blockchainaccounts_wallet_id"=>NULL,
			"blockchainaccounts_wallet_password"=>NULL,
			"blockchainaccounts_notification_key"=>md5(rand().microtime()),
			"blockchainaccounts_notifications"=>2,
			"blockchainaccounts_lastcheck"=>NULL,
			"blockchainaccounts_wallet_type"=>NULL,
			"blockchainaccounts_withdraw_processing"=>"manual",
		);

		/*error_reporting(E_ALL);
		ini_set('display_errors', 1);*/

		if (!session_id() && php_sapi_name()!="cli")
			session_start();
	}

	/**
	 * Get blockchain.info wallet.
	 */
	public function getWallet() {
		if (!$this->wallet) {
			switch (get_option("blockchainaccounts_wallet_type")) {
				case "blockchain_info":
					$this->wallet=new BlockchainWallet(
						get_option("blockchainaccounts_wallet_id"),
						get_option("blockchainaccounts_wallet_password")
					);
					break;

				case "block_io":
					$this->wallet=new BlockIoWallet(
						get_option("blockchainaccounts_block_io_api_key")
					);

					if (get_option("blockchainaccounts_withdraw_processing")=="auto")
						$this->wallet->setPassword(get_option("blockchainaccounts_block_io_password"));

					break;

				case "mock":
					$this->wallet=new MockWallet();
					break;

				default:
					throw new Exception("Wallet service not set up");
			}
		}

		return $this->wallet;
	}

	/**
	 * Activate.
	 */
	public function activate() {
		Account::install();
		Account::ensureNotificationsDirExists();
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

	/**
	 * Are we setup yet?
	 */
	public function isSetup() {
		if (!get_option("blockchainaccounts_wallet_type"))
			return FALSE;

		return TRUE;
	}
}