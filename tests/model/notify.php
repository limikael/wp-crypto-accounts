<?php

require_once __DIR__."/../../src/utils/PubSub.php";

use wpblockchainaccounts\PubSub;

if (sizeof($_SERVER["argv"])!=2)
	exit("Usage: notify <filename> <delay>");

$path=$_SERVER["argv"][1];
$dir=dirname($path);
$fn=basename($path);
$oldcwd=getcwd();
chdir($dir);
$pubSub=new PubSub($fn);

for ($i=0; $i<10; $i++) {
	$pubSub->publish();
	usleep(100);
}
chdir($oldcwd);

