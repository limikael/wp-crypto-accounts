<?php

	namespace wpblockchainaccounts;

	require_once __DIR__."/../utils/Singleton.php";
	require_once __DIR__."/../plugin/BlockChainAccountsPlugin.php";
	require_once __DIR__."/../model/Transaction.php";

	use \Exception;

	/**
	 * Api controller.
	 */
	class ApiController extends Singleton {

		/**
		 * List scheduled transactions.
		 */
		public function scheduled() {
			$transactions=Transaction::findAllBy("state",Transaction::SCHEDULED);

			$res=array();
			$res["transactions"]=array();

			foreach ($transactions as $transaction) {
				$res["transactions"][]=array(
					"id"=>$transaction->id,
					"fromAccountId"=>$transaction->fromAccountId,
					"amount"=>$transaction->amount,
					"withdrawAddress"=>$transaction->withdrawAddress
				);
			}

			$res["ok"]=1;

			return $res;
		}

		/**
		 * Begin transaction
		 */
		public function beginTransaction($p) {
			$transaction=Transaction::findOne($p["transactionId"]);

			if (!$transaction)
				throw new Exception("Transaction not found");

			if ($transaction->state!=Transaction::SCHEDULED)
				throw new Exception("Unexpected transaction state: ".$transaction->state);

			$transaction->state=Transaction::PROCESSING;
			$transaction->save();

			$res=array(
				"id"=>$transaction->id,
				"fromAccountId"=>$transaction->fromAccountId,
				"amount"=>$transaction->amount,
				"withdrawAddress"=>$transaction->withdrawAddress
			);

			$res["ok"]=1;

			return $res;
		}

		/**
		 * End transaction.
		 */
		public function endTransaction($p) {
			$transaction=Transaction::findOne($p["transactionId"]);

			if (!$transaction)
				throw new Exception("Transaction not found");

			if ($transaction->state!=Transaction::PROCESSING)
				throw new Exception("Unexpected transaction state: ".$transaction->state);

			$transaction->state=Transaction::COMPLETE;
			$transaction->save();

			$res=array();
			$res["ok"]=1;

			return $res;
		}

		/**
		 * Handle method.
		 */
		public function handle($method, $parameters=array()) {
			if ($method=="dispatch" || $method=="handle" || !ctype_alpha($method))
				$method=NULL;

			try {
				if (!method_exists($this, $method))
					throw new Exception("Unknown method: ".$method);

				if (!isset($parameters["key"]))
					throw new Exception("Need api key.");

				if ($parameters["key"]!=get_option("blockchainaccounts_notification_key"))
					throw new Exception("Wrong key.");

				$res=$this->$method($_REQUEST);

				$res["ok"]=1;
				echo json_encode($res);
			}

			catch (Exception $e) {
				echo json_encode(array(
					"ok"=>0,
					"message"=>$e->getMessage()
				));
			}
		}

		/**
		 * Dispatch call.
		 */
		public function dispatch() {
			$this->handle(basename($_SERVER["PHP_SELF"]),$_REQUEST);
		}
	}