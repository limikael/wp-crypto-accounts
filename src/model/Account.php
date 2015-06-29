<?php

	namespace wpblockchainaccounts;

	require_once __DIR__."/../../ext/smartrecord/SmartRecord.php";
	require_once __DIR__."/../utils/BitcoinUtil.php";
	require_once __DIR__."/../plugin/BlockChainAccountsPlugin.php";
	require_once __DIR__."/Transaction.php";

	use \SmartRecord;
	use \Exception;

	/**
	 * Account abstraction.
	 */
	class Account extends SmartRecord {

		/**
		 * Constructor.
		 */
		public function __construct($entity_type=NULL, $entity_id=NULL) {
			$this->entity_type=$entity_type;
			$this->entity_id=$entity_id;
			$this->balance=0;
		}

		/**
		 * Get balance.
		 */
		public function getBalance($denomination) {
			return BitcoinUtil::fromSatoshi($denomination,$this->balance);
		}

		/**
		 * Get deposit address.
		 * Create if it doesn't exist.
		 */
		public function getDepositAddress() {
			if (!$this->depositAddress) {
				$wallet=BlockChainAccountsPlugin::init()->getWallet();

				$this->depositAddress=$wallet->createNewAddress();
				$this->save();
			}

			return $this->depositAddress;
		}

		/**
		 * Get user account.
		 * It is possible to pass a user or a user id as argument.
		 */
		public static function getUserAccount($user_id) {
			if (is_object($user_id))
				$user_id=$user_id->ID;

			if (!$user_id)
				return;

			return self::getEntityAccount("user",$user_id);
		}

		/**
		 * Get account for entity.
		 * If the account doesn't exist it will be created.
		 */
		public static function getEntityAccount($entity_type, $entity_id) {
			if (!$entity_id)
				throw new Exception("Expected entity id");

			$account=self::findOneByQuery(
				"SELECT * FROM %t WHERE entity_type=%s AND entity_id=%s",
				$entity_type,
				$entity_id
			);

			if (!$account) {
				$account=new Account($entity_type, $entity_id);
				$account->save();
			}

			return $account;
		}

		/**
		 * Get account for current user.
		 */
		public static function getCurrentUserAccount() {
			$user=wp_get_current_user();

			if (!$user || !$user->ID)
				return NULL;

			$account=Account::getUserAccount($user->ID);

			return $account;
		}

		/**
		 * Get transactions for this account.
		 */
		public function getTransactions() {
			return Transaction::findAllByQuery(
				"SELECT    * ".
				"FROM      :table ".
				"WHERE     toAccountId=%s OR fromAccountId=%s ".
				"ORDER BY  timestamp DESC",
				$this->id,
				$this->id
			);
		}

		/**
		 * Withdraw funds.
		 */
		public function withdraw($denomination, $address, $amount) {
			if ($amount<0 || $amount>$this->getBalance($denomination))
				throw new Exception("Insufficient funds on account.");

			if ($this->entity_type!="user")
				throw new Exception("Can only withdraw from user accounts.");

			$this->balance-=BitcoinUtil::toSatoshi($denomination,$amount);

			$t=new Transaction();
			$t->fromAccountId=$this->id;
			$t->fromAccountBalance=$this->balance;
			$t->setAmount($denomination,$amount);
			$t->state=Transaction::SHEDULED;
			$t->notice="Withdraw";
			$t->save();

			$this->save();
		}

		/**
		 * Set up fields.
		 */
		public static function initialize() {
			self::field("id","integer not null auto_increment");
			self::field("entity_id","integer");
			self::field("entity_type","varchar(255) not null");
			self::field("balance","integer not null");
			self::field("depositAddress","varchar(255)");
		}
	}