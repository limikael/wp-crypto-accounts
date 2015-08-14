<?php

	require_once __DIR__."/../src/plugin/CryptoAccountsPlugin.php";

	use wpblockchainaccounts\CryptoAccountsPlugin;

	class AccountTest extends WP_UnitTestCase {
		public function testBasic() {
			CryptoAccountsPlugin::init()->activate();

			$user_id = $this->factory->user->create();
			$user=get_user_by("id",$user_id);

			$account=bca_user_account($user_id);
			$this->assertEquals(0,$account->getBalance("btc"));
		}
	}