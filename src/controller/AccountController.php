<?php

namespace wpblockchainaccounts;

require_once __DIR__."/../model/Account.php";

use \Exception;

/**
 * Handle account related frontend stuff.
 */
class AccountController extends Singleton {

	/**
	 * Construct.
	 */
	public function __construct() {
		add_action('wp_ajax_bca_balance_update',array($this,'balanceUpdate'));
		add_action('wp_ajax_nopriv_bca_balance_update',array($this,'balanceUpdate'));
	}

	/**
	 * Handle exception.
	 */
	public function handleException($e) {
		http_response_code(500);

		echo json_encode(array(
			"error"=>TRUE,
			"message"=>$e->getMessage()
		),JSON_PRETTY_PRINT);

		return;
	}

	/**
	 * Print account info as a response.
	 */
	public function printAccountResponse($account) {
		echo json_encode(array(
			"balance"=>$account->getBalance("satoshi"),
			"confirming"=>$account->getConfirmingAmount("satoshi"),
			"confirmingBalance"=>$account->getConfirmingBalance("satoshi")
		),JSON_PRETTY_PRINT);
	}

	/**
	 * Get balance update.
	 */
	public function balanceUpdate() {
		set_exception_handler(array($this,"handleException"));
		session_write_close();

		$account=Account::getCurrentUserAccount();
		if (!$account)
			throw new Exception("Not logged in");

		$pubSub=$account->getPubSub();
		//$pubSub->setTimeout(5);
		$pubSub->subscribe();

		$account=Account::getCurrentUserAccount();
		if ($account->getBalance("satoshi")!=$_REQUEST["balance"] ||
				$account->getConfirmingAmount("satoshi")!=$_REQUEST["confirming"] ||
				$account->getConfirmingBalance("satoshi")!=$_REQUEST["confirmingBalance"]) {
			$this->printAccountResponse($account);
			$pubSub->close();
			exit;
		}

		$pubSub->wait();
		$account=Account::getCurrentUserAccount();
		$this->printAccountResponse($account);
		exit;
	}
}