<?php

namespace wpblockchainaccounts;

require_once __DIR__."/../utils/CurlRequest.php";
require_once __DIR__."/AWallet.php";

use \Exception;

class BlockIoWallet extends AWallet {

	/**
	 * Setup.
	 */
	public function setup() {
		$curl=new CurlRequest();
		error_log("setting up");
	}
}