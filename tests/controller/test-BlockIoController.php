<?php

require_once __DIR__."/../../src/plugin/CryptoAccountsPlugin.php";
require_once __DIR__."/../../src/controller/BlockIoController.php";
require_once __DIR__."/../../src/model/Transaction.php";

use wpblockchainaccounts\CryptoAccountsPlugin;
use wpblockchainaccounts\BlockIoController;
use wpblockchainaccounts\Transaction;

/**
 * Tests for BlockIoCOntroller
 */
class BlockIoControllerTest extends WP_UnitTestCase {

	/**
	 * Set up the test.
	 */
	public function setUp() {
		parent::setUp();

		CryptoAccountsPlugin::instance()->activate();
		update_option("blockchainaccounts_wallet_type","mock");
	}

	/**
	 * If no matching account is found, throw an exception.
	 */
	public function testNoAccount() {
		$data=array(
			"type"=>"address",
			"data"=>array(
				"address"=>"hello_world",
				"txid"=>"123",
				"balance_change"=>"0.05000000",
				"confirmations"=>0,
			)
		);

		try {
			BlockIoController::instance()->process($data);
		}

		catch (Exception $e) {
			$caught=$e;
		}

		$this->assertEquals($caught->getMessage(),"No matching account.");
	}

	/**
	 * Make sure a new transaction is created if no one exists.
	 */
	public function testTransactionCreated() {
		$user_id = $this->factory->user->create();
		$account=bca_user_account($user_id);
		$address=$account->getDepositAddress();
		$this->assertTrue(strlen($address)>4);

		$data=array(
			"type"=>"address",
			"data"=>array(
				"address"=>$address,
				"txid"=>"12345678",
				"balance_change"=>"0.05000000",
				"confirmations"=>0,
			)
		);

		BlockIoController::instance()->process($data);
		$transaction=Transaction::findOneBy("transactionHash","12345678");
		$this->assertEquals(0.05,$transaction->getAmount("btc"));
		$this->assertEquals($transaction->getState(),Transaction::CONFIRMING);
		$account=bca_user_account($user_id);
		$this->assertEquals(0,$account->getBalance("btc"));
		$this->assertEquals(0.05,$account->getConfirmingBalance("btc"));

		$data=array(
			"type"=>"address",
			"data"=>array(
				"address"=>$address,
				"txid"=>"12345678",
				"balance_change"=>"0.05000000",
				"confirmations"=>"3"
			)
		);

		BlockIoController::instance()->process($data);
		$account=bca_user_account($user_id);
		$this->assertEquals(0.05,$account->getBalance("btc"));
	}
}