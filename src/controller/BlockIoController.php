<?php

namespace wpblockchainaccounts;

require_once __DIR__."/../plugin/CryptoAccountsPlugin.php";
require_once __DIR__."/../model/Transaction.php";
require_once __DIR__."/../utils/Singleton.php";
require_once __DIR__."/../utils/BitcoinUtil.php";

use \Exception;

/**
 * Handle notifications from block.io.
 */
class BlockIoController extends Singleton {

	/**
	 * Process incomming request.
	 */
	public function process($payload) {
		if ($payload["type"]!="address")
			return;

		$data=$payload["data"];

		if (!$data["txid"])
			throw new Exception("No transaction id");

		if (!$data["address"])
			throw new Exception("No address");

		if (!$data["balance_change"])
			throw new Exception("No amount data");

		$transaction=Transaction::findOneBy("transactionHash",$data["txid"]);

		if (!$transaction) {
			$account=Account::findOneBy("depositAddress",$data["address"]);
			if (!$account)
				throw new Exception("No matching account.");

			$transaction=new Transaction();
			$transaction->notice="Deposit";
			$transaction->transactionHash=$data["txid"];
			$transaction->toAccountId=$account->id;
			$transaction->state=Transaction::CONFIRMING;
			$transaction->amount=BitcoinUtil::toSatoshi("btc",$data["balance_change"]);
			$transaction->save();

			$account->getPubSub()->publish();
		}

		if ($transaction->getState()==Transaction::COMPLETE)
			return;

		$transaction->confirmations=intval($data["confirmations"]);

		$account=$transaction->getToAccount();
		if (!$account)
			throw new Exception("unable to find account");

		if ($transaction->confirmations>=get_option("blockchainaccounts_notifications")) {
			$account->balance+=$transaction->amount;
			$account->save();

			$transaction->toAccountBalance=$account->balance;
			$transaction->timestamp=time();
			$transaction->state=Transaction::COMPLETE;
			$transaction->save();
		}

		$transaction->save();
		$account->getPubSub()->publish();
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

		exit;
	}

	/**
	 * Process posted data.
	 */
	public function processPost(){
		set_exception_handler(array($this,"handleException"));

		if (!$_REQUEST["key"] ||
				$_REQUEST["key"]!=get_option("blockchainaccounts_notification_key"))
			throw new Exception("Wrong notification key.");

		$postdata=file_get_contents("php://input");
		$payload=json_decode($postdata,TRUE);
		if (!$payload)
			throw new Exception("Unable to parse json.");

		$this->process($payload);
		echo json_encode(array(
			"success"=>TRUE
		),JSON_PRETTY_PRINT);
	}
}