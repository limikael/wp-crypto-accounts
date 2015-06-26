<?php

	require_once __DIR__."/CurlUtil.php";

	/**
	 * Abstraction for managing a blockchain wallet.
	 */
	class BlockchainWallet {

		private $walletUrl;
		private $password;

		/**
		 * Constructor.
		 * The wallet url should be something like:
		 * https://blockchain.info/merchant/<walletid>
		 */
		public function BlockchainWallet($walletUrl, $password) {
			if (parse_url($walletUrl,PHP_URL_SCHEME))
				$this->walletUrl=$walletUrl;

			else
				$this->walletUrl="https://blockchain.info/merchant/".$walletUrl;

			$this->password=$password;
		}

		/**
		 * Create a new address.
		 */
		public function createNewAddress() {
			$res=CurlUtil::jsonApiRequest(
				$this->getWalletUrl()."/new_address",
				array("password"=>$this->password));

			$this->checkResponse($res);

			if (!array_key_exists("address",$res))
				throw new Exception("Unable to create new address.");

			return $res["address"];
		}

		/**
		 * Archive an address.
		 */
		public function archiveAddress($address) {
			$res=CurlUtil::jsonApiRequest(
				$this->getWalletUrl()."/archive_address",
				array(
					"password"=>$this->password,
					"address"=>$address
				));

			$this->checkResponse($res);

			if ($res["archived"]!=$address)
				throw new Exception("Unable to archive address.");
		}

		/**
		 * Get balance for specific address.
		 */
		public function getAddressBalance($address) {
			$res=CurlUtil::jsonApiRequest(
				$this->getWalletUrl()."/address_balance",
				array(
					"password"=>$this->password,
					"address"=>$address
				));

			$this->checkResponse($res);

			if (!array_key_exists("balance",$res))
				throw new Exception("Unable to fetch balance.");

			return $res["balance"];
		}

		/**
		 * Get total balance.
		 */
		public function getBalance() {
			$res=CurlUtil::jsonApiRequest(
				$this->getWalletUrl()."/balance",
				array("password"=>$this->password));

			$this->checkResponse($res);

			if (!array_key_exists("balance",$res))
				throw new Exception("Unable to get balance from blockchain.");

			return $res["balance"];
		}

		/**
		 * List addresses.
		 */
		public function getAddressList() {
			$res=CurlUtil::jsonApiRequest(
				$this->getWalletUrl()."/list",
				array("password"=>$this->password));

			$this->checkResponse($res);

			if (!array_key_exists("addresses",$res))
				throw new Exception("Unable to get address list from blockchain.");

			return $res["addresses"];
		}

		/**
		 * Send from specific address.
		 */
		public function sendFrom($fromAddress, $toAddress, $amount, $fee=NULL) {
			$params=array();

			$params["from"]=$fromAddress;
			$params["to"]=$toAddress;
			$params["amount"]=$amount;
			$params["password"]=$this->password;

			if ($fee)
				$params["fee"]=$fee;

			$res=CurlUtil::jsonApiRequest($this->getWalletUrl()."/payment",$params);
			$this->checkResponse($res);

			if (!$res["tx_hash"])
				throw new Exception("Unknown blockchain error");

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
		 * Get wallet url.
		 */
		private function getWalletUrl() {
			return $this->walletUrl."/";
			//return BlockChainWallet::API_URL."/".$this->walledId."/";
		}
	}