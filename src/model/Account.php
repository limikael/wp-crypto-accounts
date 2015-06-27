<?php

	require_once __DIR__."/../utils/ActiveRecord.php";
	require_once __DIR__."/../utils/BitcoinUtil.php";
	require_once __DIR__."/../plugin/BlockChainAccountsPlugin.php";
	require_once __DIR__."/Transaction.php";

	/**
	 * Account abstraction.
	 */
	class Account extends ActiveRecord {

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
		 */
		public static function getUserAccount($user_id) {
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
				"SELECT * FROM ".self::getFullTableName()." WHERE entity_type=:type AND entity_id=:id",
				array(
					":type"=>$entity_type,
					":id"=>$entity_id
			));

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
		 * Set up fields.
		 */
		public static function initialize() {
			self::addField("id","integer not null auto_increment");
			self::addField("entity_id","integer");
			self::addField("entity_type","varchar(255) not null");
			self::addField("balance","integer not null");
			self::addField("depositAddress","varchar(255)");
		}

		/**
		 * Get transactions for this account.
		 */
		public function getTransactions() {
			return Transaction::findAllByQuery(
				"SELECT    * ".
				"FROM      :table ".
				"WHERE     toAccountId=:toId OR fromAccountId=:fromId ".
				"ORDER BY  timestamp DESC",
				array(
					"toId"=>$this->id,
					"fromId"=>$this->id
				)
			);
		}
	}