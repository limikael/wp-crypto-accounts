<?php

namespace wpblockchainaccounts;

require_once __DIR__."/AWallet.php";

/**
 * Mock wallet for testing.
 */
class MockWallet extends AWallet {

	/**
	 * Create new address.
	 */
	public function createNewAddress() {
		return uniqid();
	}	

	/**
	 * Send.
	 */
	public function send($address, $amount) {
		return "this-is-the-hash";
	}
}