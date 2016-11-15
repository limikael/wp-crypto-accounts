<?php

require_once __DIR__."/../../src/utils/PubSub.php";

use wpblockchainaccounts\PubSub;

if (sizeof($_SERVER["argv"])!=3)
	exit("Usage: notify <filename> <data>");

$pubSub=new PubSub($_SERVER["argv"][1]);

for ($i=0; $i<10; $i++) {
	$pubSub->publish($_SERVER["argv"][2]);
	usleep(1000000*.1);
}

