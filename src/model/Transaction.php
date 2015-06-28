<?php

	require_once __DIR__."/../utils/ActiveRecord.php";
	require_once __DIR__."/../utils/BitcoinUtil.php";
	require_once __DIR__."/../plugin/BlockChainAccountsPlugin.php";

	/**
	 * An account transaction.
	 */
	class Transaction extends ActiveRecord {

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
			self::addField("id","integer not null auto_increment");
			self::addField("amount","integer not null");
			self::addField("fromAccountId","integer");
			self::addField("toAccountId","integer");
			self::addField("notice","text");
			self::addField("timestamp","integer not null");
			self::addField("transactionHash","varchar(255)");
			self::addField("state","varchar(32) not null");
			self::addField("confirmations","integer");
			self::addField("fromAccountBalance","integer");
			self::addField("toAccountBalance","integer");
		}
	}