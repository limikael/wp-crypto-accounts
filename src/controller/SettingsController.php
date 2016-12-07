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
		register_setting("blockchainaccounts","blockchainaccounts_block_io_password");
		register_setting("blockchainaccounts","blockchainaccounts_wallet_type");
		register_setting("blockchainaccounts","blockchainaccounts_withdraw_processing");
	}

	/**
	 * Create the settings page.
	 */
	public function create_settings_page() {
		$template=new Template(__DIR__."/../template/settings.tpl.php");

		if (isset($_REQUEST["settings-updated"]) && $_REQUEST["settings-updated"]) {
			if (CryptoAccountsPlugin::instance()->isSetup()) {
				$wallet=CryptoAccountsPlugin::instance()->getWallet();

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

		$tab="setup";
		if (isset($_REQUEST["tab"]))
			$tab=$_REQUEST["tab"];

		if ($tab=="withdraw" && $_REQUEST["transactionIds"]) {
			$wallet=CryptoAccountsPlugin::instance()->getWallet();
			$wallet->setPassword($_REQUEST["password"]);

			try {
				foreach ($_REQUEST["transactionIds"] as $transactionId) {
					$transaction=Transaction::findOne($transactionId);
					$transaction->performWithdraw();
				}

				$template->set("message","Transactions completed.");
			}

			catch (Exception $e) {
				$template->set("error",$e->getMessage());
			}

		}

		if ($tab=="withdraw") {
			$denomination="btc";
			$totalAmount=0;
			$transactionViews=array();
			$transactions=Transaction::findAllBy("state",Transaction::SCHEDULED);
			foreach ($transactions as $transaction) {
				$user=$transaction->getFromAccount()->getUser();

				$transactionView=array(
					"id"=>$transaction->id,
					"when"=>human_time_diff(time(),$transaction->timestamp)." ago",
					"user"=>$user->display_name." (".$user->user_email.")",
					"amount"=>$transaction->getAmount($denomination)." ".$denomination,
				);

				$transactionViews[]=$transactionView;
				$totalAmount+=$transaction->getAmount($denomination);
			}

			$wallet=CryptoAccountsPlugin::instance()->getWallet();
			$template->set("passwordLabel",$wallet->getPasswordLabel());
			$template->set("transactions",$transactionViews);
		}

		if ($tab=="accounts") {
			if (isset($_REQUEST["type"]) && isset($_REQUEST["id"]) &&
					$_REQUEST["type"] && $_REQUEST["id"])
				$account=Account::getExistingEntityAccount($_REQUEST["type"],$_REQUEST["id"]);

			else
				$account=NULL;

			if ($account)
				$template->set("balance",$account->getBalance("btc")." btc");

			else
				$template->set("balance","Select account to view");

			$template->set("type",isset($_REQUEST["type"])?$_REQUEST["type"]:"");
			$template->set("id",isset($_REQUEST["id"])?$_REQUEST["id"]:"");

			$transactionViews=array();

			if ($account) {
				$transactions=$account->getTransactions();
				foreach ($transactions as $transaction) {
					$transactionView=array(
						"notice"=>$transaction->getNotice(),
						"entity"=>$transaction->getOtherEntityString($account),
						"when"=>human_time_diff(time(),$transaction->timestamp)." ago",
						"amount"=>$transaction->getAmountForAccount("btc",$account)." btc"
					);

					$transactionViews[]=$transactionView;
				}
			}
			$template->set("transactions",$transactionViews);
		}

		$template->set("tab",$tab);
		$template->set("totalAmount",$totalAmount." ".$denomination);
		$template->show();
	}
}