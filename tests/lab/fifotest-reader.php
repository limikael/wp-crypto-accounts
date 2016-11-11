<?php

//posix_mkfifo(__DIR__."/test.fifo", 0644);

echo "made..\n";

$f=fopen(__DIR__."/test.fifo","r");
echo "opened...\n";
//stream_set_timeout($f,5);

/*$r=array($f);
$w=array();
$x=array();

stream_select($r,$w,$x,1);*/

$s=fgets($f);
echo $s;
fclose($f);