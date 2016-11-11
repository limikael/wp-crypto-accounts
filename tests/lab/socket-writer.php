<?php

$socket=socket_create(AF_UNIX,SOCK_STREAM,0);

$res=socket_connect($socket,__DIR__."/test.socket");
if (!$res)
	exit("can't connect");
