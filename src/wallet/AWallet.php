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
}