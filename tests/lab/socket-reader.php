<?php

if (file_exists(__DIR__."/test.socket"))
	unlink(__DIR__."/test.socket");

$socket=socket_create(AF_UNIX,SOCK_STREAM,0);

$res=socket_bind($socket,__DIR__."/test.socket");

if (!$res)
	exit("can't bind");

$res=socket_listen($socket,10);
if (!$res)
	exit("can't listen");

sleep(10);

/*$r=array($socket);
$w=array();
$x=array();

$sel=socket_select($r,$w,$x,10);
echo "sel: $sel\n";*/

//$f=socket_accept($socket);

