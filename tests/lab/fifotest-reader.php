<?php

if (!file_exists(__DIR__."/test.fifo"))
	posix_mkfifo(__DIR__."/test.fifo", 0644);

echo "made..\n";

$f=fopen(__DIR__."/test.fifo","rn");
echo "opened...\n";

//stream_set_blocking($f, TRUE);
//stream_set_timeout($f,5);

$r=array($f);
$w=array();
$x=array();

stream_select($r,$w,$x,30);

echo "here...\n";

$s=fgets($f);
echo "got: $s\n";
fclose($f);