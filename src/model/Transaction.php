<?php

	namespace wpblockchainaccounts;

	require_once __DIR__."/../../ext/wprecord/WpRecord.php";
	require_once __DIR__."/../utils/BitcoinUtil.php";
	require_once __DIR__."/../plugin/CryptoAccountsPlugin.php";
	require_once __DIR__."/Account.php";

	use \WpRecord;
	use \Exception;

	/**
	 * An account transaction.
	 */
	class Transaction extends WpRecord {

		const PROCESSING="processing";
		const COMPLETE="complete";
		const CONFIRMING="confirming";
		const SCHEDULED="scheduled";

		/**
		 * Construct.
		 */
		public function __construct() {
			$this->timestamp=time();
			$this->state=Transaction::PROCESSING;
		}

		/**
		 * Get from account.
		 */
		public function getFromAccount() {
			if (!$this->fromAccount)
				$this->fromAccount=Account::findOne($this->fromAccountId);

			return $this->fromAccount;
		}

		/**
		 * Get to account.
		 */
		public function getToAccount() {
			if (!$this->toAccount)
				$this->toAccount=Account::findOne($this->toAccountId);

			return $this->toAccount;
		}

		/**
		 * Set to account.
		 */
		public function setToAccount($account) {
			$this->toAccount=$account;
			$this->toAccountId=$account->id;
		}

		/**
		 * Set from account.
		 */
		public function setFromAccount($account) {
			$this->fromAccount=$account;
			$this->fromAccountId=$account->id;
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
		 * Perform transaction.
		 */
		public function perform() {
			if ($this->state!=Transaction::PROCESSING)
				throw new Exception("Unexpected transaction state: ".$this->state);

			if ($this->amount<0)
				throw new Exception("Negative amount for transaction.");

			$toAccount=$this->getToAccount();
			$fromAccount=$this->getFromAccount();

			if ($fromAccount->balance<$this->amount)
				throw new Exception("Insufficient funds on account.");

			$fromAccount->balance-=$this->amount;
			$toAccount->balance+=$this->amount;
			$this->fromAccountBalance=$fromAccount->balance;
			$this->toAccountBalance=$toAccount->balance;
			$this->state=Transaction::COMPLETE;

			$fromAccount->save();
			$toAccount->save();
			$this->save();
		}

		/**
		 * Get number of transactions in state.
		 */
		public static function getNumTransactionsForState($state) {
			global $wpdb;

			$q=$wpdb->prepare("SELECT COUNT(*) FROM ".self::getFullTableName()." WHERE state=%s",$state);
			$res=$wpdb->get_var($q);

			return $res;
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
			self::field("withdrawAddress","varchar(255)");
		}
	}