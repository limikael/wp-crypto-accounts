<?php

require_once __DIR__."/../../src/utils/PubSub.php";

$channel=new PubSub(__DIR__."/test.channel");
$channel->publish(array(
	"id"=>"10"
));