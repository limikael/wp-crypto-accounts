<?php

require_once __DIR__."/../../src/utils/PubSub.php";

$channel=new PubSub(__DIR__."/test.channel");
//$channel->subscribe();
$data=$channel->wait();

error_log("got data: ".print_r($data,TRUE));
print_r($data);