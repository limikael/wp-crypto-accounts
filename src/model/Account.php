<?php

namespace wpblockchainaccounts;

require_once __DIR__."/../../ext/wprecord/WpRecord.php";
require_once __DIR__."/../utils/BitcoinUtil.php";
require_once __DIR__."/../utils/PubSub.php";
require_once __DIR__."/../plugin/CryptoAccountsPlugin.php";
require_once __DIR__."/Transaction.php";

use \WpRecord;
use \Exception;

/**
 * Account abstraction.
 */
class Account extends WpRecord {

	/**
	 * Constructor.
	 */
	public function __construct($entity_type=NULL, $entity_id=NULL) {
		$this->entity_type=$entity_type;
		$this->entity_id=$entity_id;
		$this->balance=0;
		$this->depositAddress=NULL;
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
		if (!CryptoAccountsPlugin::instance()->isSetup())
			return NULL;

		if (!$this->depositAddress) {
			$wallet=CryptoAccountsPlugin::instance()->getWallet();

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
		$t->withdrawAddress=$address;
		$t->fromAccountId=$this->id;
		$t->fromAccountBalance=$this->balance;
		$t->setAmount($denomination,$amount);
		$t->state=Transaction::SCHEDULED;
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

	/**
	 * Get dir for notifications.
	 */
	public static function getNotificationsDir() {
		$upload_dir=wp_get_upload_dir();
		$dir=$upload_dir["basedir"]."/crypto-accounts-notifications/";

		//return sys_get_temp_dir()."/wp-ca-n/";

		return $dir;
	}

	/**
	 * Ensure notifications dir exists.
	 */
	public static function ensureNotificationsDirExists() {
		$dir=Account::getNotificationsDir();
		if (!is_dir($dir) && !mkdir($dir))
			throw new Exception("Unable to create dir: ".$dir);
	}

	/**
	 * Get pub sub.
	 */
	public function getPubSubFileName() {
		return $this->entity_type.":".$this->entity_id;
	}

	/**
	 * Notify listeners there is a change on the account.
	 */
	public function notifyChange() {
		Account::ensureNotificationsDirExists();

		$oldcwd=getcwd();
		chdir(Account::getNotificationsDir());
		$pubSub=new PubSub($this->getPubSubFileName());
		$pubSub->publish();
		chdir($oldcwd);
	}

	/**
	 * Wait for change.
	 */
	public function waitChange() {
		Account::ensureNotificationsDirExists();

		$oldcwd=getcwd();
		chdir(Account::getNotificationsDir());
		$fn=$this->entity_type.":".$this->entity_id;
		$pubSub=new PubSub(Account::getNotificationsDir()."/".$fn);
		$pubSub->wait();
		chdir($oldcwd);
	}
}