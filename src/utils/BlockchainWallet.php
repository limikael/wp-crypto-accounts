<?php

	namespace wpblockchainaccounts;

	require_once __DIR__."/CurlRequest.php";

	use \Exception;

	/**
	 * Abstraction for managing a blockchain wallet.
	 */
	class BlockchainWallet {

		private $walletUrl;
		private $password;
		private $secondPassword;

		/**
		 * Constructor.
		 * The wallet url should be something like:
		 * https://blockchain.info/merchant/<walletid>
		 */
		public function __construct($walletUrl, $password) {
			if (parse_url($walletUrl,PHP_URL_SCHEME))
				$this->walletUrl=$walletUrl;

			else
				$this->walletUrl="https://blockchain.info/merchant/".$walletUrl;

			$this->password=$password;
		}

		/**
		 * Set second password.
		 */
		public function setSecondPassword($secondPassword) {
			$this->secondPassword=$secondPassword;
		}

		/**
		 * Create a new address.
		 */
		public function createNewAddress() {
			$res=$this->createRequest("new_address")->exec();
			$this->checkResponse($res);

			if (!array_key_exists("address",$res))
				throw new Exception("Unable to create new address.");

			return $res["address"];
		}

		/**
		 * Archive an address.
		 */
		public function archiveAddress($address) {
			$req=$this->createRequest("archive_address");
			$req->setParam("address",$address);

			$res=$req->exec();
			$this->checkResponse($res);

			if ($res["archived"]!=$address)
				throw new Exception("Unable to archive address.");
		}

		/**
		 * Get balance for specific address.
		 */
		public function getAddressBalance($address) {
			$req=$this->createRequest("address_balance");
			$req->setParam("address",$address);

			$res=$req->exec();
			$this->checkResponse($res);

			if (!array_key_exists("balance",$res))
				throw new Exception("Unable to fetch balance.");

			return $res["balance"];
		}

		/**
		 * Get total balance.
		 */
		public function getBalance() {
			$res=$this->createRequest("balance")->exec();
			$this->checkResponse($res);

			if (!array_key_exists("balance",$res))
				throw new Exception("Unable to get balance from blockchain.");

			return $res["balance"];
		}

		/**
		 * List addresses.
		 */
		public function getAddressList() {
			$res=$this->createRequest("list")->exec();
			$this->checkResponse($res);

			if (!array_key_exists("addresses",$res))
				throw new Exception("Unable to get address list from blockchain.");

			return $res["addresses"];
		}

		/**
		 * Send from specific address.
		 */
		public function sendFrom($fromAddress, $toAddress, $amount, $fee=NULL) {
			$req=$this->createRequest("payment");
			$req->setParam("from",$fromAddress);
			$req->setParam("to",$toAddress);
			$req->setParam("amount",$amount);

			if ($fee)
				$req->setParam("fee",$fee);

			$res=$req->exec();
			$this->checkResponse($res);

			if (!$res["tx_hash"])
				throw new Exception("Unknown blockchain error");

			return $res;
		}

		/**
		 * Send to address.
		 */
		public function send($toAddress, $amount, $fee=NULL) {
			$req=$this->createRequest("payment");
			$req->setParam("to",$toAddress);
			$req->setParam("amount",$amount);

			if ($fee)
				$req->setParam("fee",$fee);

			$res=$req->exec();
			$this->checkResponse($res);

			if (!$res["tx_hash"])
				throw new Exception("Blockchain error");

			return $res;
		}

		/**
		 * Check the response for errors.
		 */
		private function checkResponse($res) {
			if (array_key_exists("error",$res))
				throw new Exception("Blockchain API failiure: ".$res["error"]);
		}

		/**
		 * Ends with.
		 */
		private static function endsWith($haystack, $needle) {
			return 
				$needle === "" || 
				(($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
		}

		/**
		 * Get wallet url.
		 */
		private function getWalletUrl() {
			$w=$this->walletUrl;

			if (!self::endsWith($w,"/"))
				$w.="/";

			return $w;
		}

		/**
		 * Create request for method.
		 */
		private function createRequest($method) {
			$req=new CurlRequest($this->getWalletUrl().$method);
			$req->setResultProcessing(CurlRequest::JSON);
			$req->setParam("password",$this->password);

			if ($this->secondPassword)
				$req->setParam("second_password",$this->secondPassword);

			return $req;
		}
	}