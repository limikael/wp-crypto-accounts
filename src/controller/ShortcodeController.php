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
			add_shortcode("bca_balance", array($this, "bca_balance"));
			add_shortcode("bca_deposit", array($this, "bca_deposit"));
			add_shortcode("bca_history", array($this, "bca_history"));

			wp_register_script("blockchainaccounts-jquery-qrcode",
				plugins_url()."/wpblockchainaccounts/res/jquery.qrcode-0.12.0.min.js");

			wp_register_style('wpblockchainaccounts', 
				plugins_url()."/wpblockchainaccounts/res/wpblockchainaccounts.css");
		}

		/**
		 * Show user balance.
		 */
		public function bca_balance() {
			$account=Account::getCurrentUserAccount();

			if (!$account)
				return "<i>not logged in</i>";

			return $account->getBalance();
		}

		/**
		 * Show deposit address.
		 */
		public function bca_deposit() {
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

		/**
		 * History.
		 */
		public function bca_history() {
			wp_enqueue_style("wpblockchainaccounts");

			$account=Account::getCurrentUserAccount();

			if (!$account)
				return "<i>not logged in</i>";

			$transactions=array();

			foreach ($account->getTransactions() as $item) {
				$transaction=array(
					"item"=>$item,
					"amount"=>$item->getAmountForAccount($account),
					"balance"=>$item->getBalanceForAccount($account)
				);

				$transaction["time"]=date("Y-m-d",$item->timestamp);
				if ($transaction["time"]==date("Y-m-d"))
					$transaction["time"]=date("H:i",$item->timestamp);

				$transactions[]=$transaction;
			}

			$template=new Template(__DIR__."/../template/history.tpl.php");
			$template->set("transactions",$transactions);

			return $template->render();
		}
	}