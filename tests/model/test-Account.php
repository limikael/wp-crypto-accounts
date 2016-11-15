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

		$fn=Account::getNotificationsDir()."/".$account->getPubSubFileName();
		$cmd="/usr/bin/php ".__DIR__."/notify.php ".$fn." > /dev/null 2>&1 &";
		system($cmd,$res);

		$account->waitChange();

		if (time()-$t>10)
			throw new Exception("That took too long");
	}
}