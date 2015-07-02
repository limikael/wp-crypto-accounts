<?php

	namespace wpblockchainaccounts;

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
			add_shortcode("bca_withdraw", array($this, "bca_withdraw"));

			wp_register_script("blockchainaccounts-jquery-qrcode",
				plugins_url()."/wpblockchainaccounts/res/jquery.qrcode-0.12.0.min.js");

			wp_register_style('wpblockchainaccounts', 
				plugins_url()."/wpblockchainaccounts/res/wpblockchainaccounts.css");

			add_action('wp_enqueue_scripts',array($this,'enqueue_scripts'));
		}

		/**
		 * Enqueue scripts.
		 */
		public function enqueue_scripts() {
			wp_enqueue_script("jquery");
		}

		/**
		 * Show user balance.
		 */
		public function bca_balance($p) {
			if (!isset($p["denomination"]))
				$p["denomination"]="bits";

			$account=Account::getCurrentUserAccount();

			if (!$account)
				return "<i>not logged in</i>";

			return $account->getBalance($p["denomination"])." ".$p["denomination"];
		}

		/**
		 * Show deposit address.
		 */
		public function bca_deposit() {
			if (!BlockChainAccountsPlugin::init()->isSetup())
				return "<i>Accounts are not set up</i>";

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
		public function bca_history($p) {
			if (!BlockChainAccountsPlugin::init()->isSetup())
				return "<i>Accounts are not set up</i>";

			$oldTimezone=date_default_timezone_get();
			date_default_timezone_set(get_option('timezone_string'));

			wp_enqueue_style("wpblockchainaccounts");

			if (!isset($p["denomination"]))
				$p["denomination"]="bits";

			$denom=$p["denomination"];
			$account=Account::getCurrentUserAccount();

			if (!$account)
				return "<i>not logged in</i>";

			$transactions=array();

			foreach ($account->getTransactions() as $item) {
				$transaction=array(
					"item"=>$item,
					"amount"=>$item->getAmountForAccount($denom,$account)." $denom",
					"balance"=>$item->getBalanceForAccount($denom,$account)." $denom"
				);

				$transaction["time"]=date("Y-m-d",$item->timestamp);
				if ($transaction["time"]==date("Y-m-d"))
					$transaction["time"]=date("H:i",$item->timestamp);

				$transactions[]=$transaction;
			}

			$template=new Template(__DIR__."/../template/history.tpl.php");
			$template->set("transactions",$transactions);

			date_default_timezone_set($oldTimezone);

			return $template->render();
		}

		/**
		 * Withdraw.
		 */
		public function bca_withdraw($p) {
			if (!BlockChainAccountsPlugin::init()->isSetup())
				return "<i>Accounts are not set up</i>";

			if (!isset($p["denomination"]))
				$p["denomination"]="bits";

			wp_enqueue_style("wpblockchainaccounts");

			$denom=$p["denomination"];
			$account=Account::getCurrentUserAccount();

			if (!$account)
				return "<i>not logged in</i>";

			$afterWithdraw=$_SERVER["PHP_SELF"];

			if ($p["submit_attributes"])
				$afterWithdraw.="?".$p["submit_attributes"];

			$template=new Template(__DIR__."/../template/withdraw.tpl.php");
			$template->set("denomination",$p["denomination"]);
			$template->set("action",plugins_url()."/wpblockchainaccounts/withdraw.php");
			$template->set("afterWithdraw",$afterWithdraw);
			$template->set("amount","");
			$template->set("address","");
			$template->set("showForm",TRUE);

			if (isset($_SESSION["bca_withdraw_error"]))
				$template->set("error",$_SESSION["bca_withdraw_error"]);

			if (isset($_SESSION["bca_withdraw_amount"]))
				$template->set("amount",$_SESSION["bca_withdraw_amount"]);

			if (isset($_SESSION["bca_withdraw_address"]))
				$template->set("address",$_SESSION["bca_withdraw_address"]);

			if (isset($_SESSION["bca_withdraw_success"])) {
				$template->set("success",$_SESSION["bca_withdraw_success"]);
				$template->set("showForm",FALSE);
				$template->set("action",$afterWithdraw);
			}

			unset($_SESSION["bca_withdraw_success"]);
			unset($_SESSION["bca_withdraw_error"]);
			unset($_SESSION["bca_withdraw_amount"]);
			unset($_SESSION["bca_withdraw_address"]);

			return $template->render();
		}
	}