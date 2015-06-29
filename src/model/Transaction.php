<?php

	namespace wpblockchainaccounts;

	require_once __DIR__."/../../ext/smartrecord/SmartRecord.php";
	require_once __DIR__."/../utils/BitcoinUtil.php";
	require_once __DIR__."/../plugin/BlockChainAccountsPlugin.php";

	use \SmartRecord;

	/**
	 * An account transaction.
	 */
	class Transaction extends SmartRecord {

		const PROCESSING="processing";
		const COMPLETE="complete";
		const CONFIRMING="confirming";
		const SHEDULED="sheduled";

		/**
		 * Construct.
		 */
		public function __construct() {
			$this->timestamp=time();
			$this->state=Transaction::PROCESSING;
		}

		/**
		 * Get balance for affected account.
		 */
		public function getBalanceForAccount($denomination, $account) {
			if ($account->id==$this->toAccountId)
				return BitcoinUtil::fromSatoshi($denomination, $this->toAccountBalance);

			if ($account->id==$this->fromAccountId)
				return BitcoinUtil::fromSatoshi($denomination, $this->fromAccountBalance);

			return NULL;
		}

		/**
		 * Get amount for affected account.
		 */
		public function getAmountForAccount($denomination, $account) {
			if ($account->id==$this->toAccountId)
				return BitcoinUtil::fromSatoshi($denomination, $this->amount);

			if ($account->id==$this->fromAccountId)
				return -BitcoinUtil::fromSatoshi($denomination, $this->amount);

			return NULL;
		}

		/**
		 * Set amount.
		 */
		public function setAmount($denomination, $amount) {
			$this->amount=BitcoinUtil::toSatoshi($denomination,$amount);
		}

		/**
		 * Set up fields.
		 */
		public static function initialize() {
			self::field("id","integer not null auto_increment");
			self::field("amount","integer not null");
			self::field("fromAccountId","integer");
			self::field("toAccountId","integer");
			self::field("notice","text");
			self::field("timestamp","integer not null");
			self::field("transactionHash","varchar(255)");
			self::field("state","varchar(32) not null");
			self::field("confirmations","integer");
			self::field("fromAccountBalance","integer");
			self::field("toAccountBalance","integer");
		}
	}