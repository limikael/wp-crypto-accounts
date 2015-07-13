<?php

	require_once __DIR__."/../vendor/autoload.php";
	require_once __DIR__."/../src/wpca/Wpca.php";
	require_once __DIR__."/../src/controller/ApiController.php";
	require_once __DIR__."/../src/utils/BitcoinUtil.php";

	use wpblockchainaccounts\Wpca;
	use wpblockchainaccounts\ApiController;
	use wpblockchainaccounts\BlockChainAccountsPlugin;
	use wpblockchainaccounts\BitcoinUtil;
	use wpblockchainaccounts\CurlRequest;
	use wpblockchainaccounts\BlockchainWallet;

	use blockchainwalletmock\BlockchainWalletMock;

	class WpcaTest extends WP_UnitTestCase {
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

			$mockWalletServer=new BlockchainWalletMock();
			$mockWalletServer->setPort(8910);
			$mockWalletServer->setDefaultFee(10);
			$mockWalletServer->runInBackground();

			$wallet=new BlockchainWallet("http://localhost:8910","test");
			$address=$wallet->createNewAddress();

			$res=(new CurlRequest("http://localhost:8910/debug_incoming"))
				->setResultProcessing(CurlRequest::JSON)
				->setParam("address",$address)
				->setParam("amount",10000)
				->exec();

			$wpca=new Wpca();
			$wpca->setWalletId("http://localhost:8910/");
			$wpca->setWalletPassword("hello");
			$wpca->setMockApi(ApiController::init());
			ob_start();
			$wpca->status();
			$contents=ob_get_contents();
			ob_end_clean();

			$this->assertEquals($contents,"Transaction queue has 1 transaction(s), the total amount is 5000 satoshi.\n");

			$wpca->process();
			$this->assertEquals(4990,$wallet->getBalance());

			$account=bca_user_account($user_id);
			$this->assertEquals(50,$account->getBalance("bits"));

			ob_start();
			$wpca->status();
			$contents=ob_get_contents();
			ob_end_clean();

			$this->assertEquals($contents,"Transaction queue has 0 transaction(s), the total amount is 0 satoshi.\n");

			$mockWalletServer->stop();
		}
	}