<?php

	// Not an automatic test...

	require_once __DIR__."/../src/utils/BlockchainWallet.php";

	use wpblockchainaccounts\BlockchainWallet;

	$wallet=new BlockchainWallet(getenv("BLOCKCHAIN_ID"),getenv("BLOCKCHAIN_PW"));

	//echo "accessing: ".$wallet->getWalletUrl()."\n";

	echo "addresses: ".sizeof($wallet->getAddressList())."\n";