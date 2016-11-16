<?php

namespace wpblockchainaccounts;

/**
 * Represents a wallet service.
 */
abstract class AWallet {

	/**
	 * Set up
	 */
	public function setup() {
	}

	/**
	 * Create a new address.
	 */
	abstract function createNewAddress();

	/**
	 * Send.
	 * Should return the transaction hash.
	 */
	abstract function send($toAddress, $amount);

	/**
	 * Get password label.
	 */
	function getPasswordLabel() {
		return "Wallet Password";
	}
}