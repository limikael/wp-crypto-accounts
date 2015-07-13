<?php

	require_once __DIR__."/../vendor/autoload.php";

	require_once __DIR__."/../src/plugin/BlockChainAccountsPlugin.php";
	require_once __DIR__."/../src/controller/ApiController.php";
	require_once __DIR__."/../src/utils/BitcoinUtil.php";

	use wpblockchainaccounts\BlockChainAccountsPlugin;
	use wpblockchainaccounts\Apicontroller;
	use wpblockchainaccounts\BitcoinUtil;

	use blockchainwalletmock\BlockchainWalletMock;

	class ApiTest extends WP_UnitTestCase {
		public function testScheduled() {
			BlockChainAccountsPlugin::init()->activate();

			$user_id = $this->factory->user->create();
			$user=get_user_by("id",$user_id);

			$account=bca_user_account($user_id);
			$this->assertEquals(0,$account->getBalance("btc"));

			$account->balance=BitcoinUtil::toSatoshi("bits",100);
			$account->save();

			$account->withdraw("bits","asdf",50);

			$p=array(
				"key"=>get_option("blockchainaccounts_notification_key")
			);

			ob_start();
			ApiController::init()->handle("scheduled",$p);
			$out=ob_get_contents();
			ob_end_clean();

			$res=json_decode($out,TRUE);
			//print_r($res);

			$this->assertEquals(1,sizeof($res["transactions"]));
		}
	}