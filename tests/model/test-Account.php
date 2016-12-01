<?php

require_once __DIR__."/../../src/plugin/CryptoAccountsPlugin.php";
require_once __DIR__."/../../src/model/Account.php";

use wpblockchainaccounts\CryptoAccountsPlugin;
use wpblockchainaccounts\Account;

/**
 * Test for the account class.
 */
class AccountTest extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		CryptoAccountsPlugin::instance()->activate();
	}

	/**
	 * Smoke test.
	 */
	public function testBasic() {
		$user_id = $this->factory->user->create();
		$user=get_user_by("id",$user_id);

		$account=bca_user_account($user_id);
		$this->assertEquals(0,$account->getBalance("btc"));
	}

	/**
	 * Test notifications.
	 */
	public function testNotifications() {
		$t=time();
		$user_id = $this->factory->user->create();
		$account=bca_user_account($user_id);

		$fn=$account->getPubSub()->getFileName();
		$cmd="/usr/bin/php ".__DIR__."/notify.php ".$fn." hello > /dev/null 2>&1 &";
		system($cmd,$res);

		$data=$account->getPubSub()->wait();
		$this->assertEquals($data,"hello");

		if (time()-$t>10)
			throw new Exception("That took too long");
	}

	/**
	 *
	 */
	public function testGetTransactions() {
		$user1_id = $this->factory->user->create();
		$user1=get_user_by("id",$user1_id);
		$user2_id = $this->factory->user->create();
		$user2=get_user_by("id",$user2_id);

		$account1=bca_user_account($user1_id);
		$account1->balance=100;
		$account1->save();

		$account1=bca_user_account($user1_id);
		$this->assertEquals(100,$account1->getBalance("satoshi"));

		$account2=bca_user_account($user2_id);
		bca_make_transaction("satoshi",$account1,$account2,100);

		$account2=bca_user_account($user2_id);
		$this->assertEquals(100,$account2->getBalance("satoshi"));

		$transactions=$account2->getTransactions();
		$this->assertCount(1,$transactions);
		$transaction=$transactions[0];

		$this->assertCount(1,$account2->getTransactions(array(
			"newerThan"=>$transaction->timestamp-1
		)));

		$this->assertCount(0,$account2->getTransactions(array(
			"newerThan"=>$transaction->timestamp
		)));

		$this->assertCount(0,$account2->getTransactions(array(
			"newerThan"=>$transaction->timestamp+1
		)));
	}
}