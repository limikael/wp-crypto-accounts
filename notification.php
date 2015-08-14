<?php

	require_once __DIR__."/src/utils/WpUtil.php";
	require_once __DIR__."/src/model/Transaction.php";
	require_once __DIR__."/src/model/Account.php";

	use wpblockchainaccounts\WpUtil;
	use wpblockchainaccounts\Transaction;
	use wpblockchainaccounts\Account;

	require_once WpUtil::getWpLoadPath();

	if ($_REQUEST["key"]!=get_option("blockchainaccounts_notification_key"))
		exit("Wrong key");

	$transaction=Transaction::findOneBy("transactionHash",$_REQUEST["transaction_hash"]);

	if (!$_REQUEST["input_address"])
		exit("expected input address");

	if (!$_REQUEST["transaction_hash"])
		exit("expected transaction_hash");

	if ($_REQUEST["value"]<0)
		exit("*ok*");

	if (!$transaction) {
		$account=Account::findOneBy("depositAddress",$_REQUEST["input_address"]);

		if (!$account)
			exit("no associated account or transaction");

		$transaction=new Transaction();
		$transaction->notice="Deposit";
		$transaction->transactionHash=$_REQUEST["transaction_hash"];
		$transaction->toAccountId=$account->id;
		$transaction->state=Transaction::CONFIRMING;
		$transaction->amount=$_REQUEST["value"];
		$transaction->save();
	}

	if ($transaction->state==Transaction::COMPLETE)
		exit("*ok*");

	$transaction->confirmations=$_REQUEST["confirmations"];

	if ($transaction->confirmations>=get_option("blockchainaccounts_notifications")) {
		$account=Account::findOneBy("id",$transaction->toAccountId);

		if (!$account)
			exit("unable to find account");

		$account->balance+=$transaction->amount;
		$account->save();

		$transaction->toAccountBalance=$account->balance;
		$transaction->timestamp=time();
		$transaction->state=Transaction::COMPLETE;
		$transaction->save();

		exit("*ok*");
	}

	$transaction->save();

	echo "processing...";