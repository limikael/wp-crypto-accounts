<?php

//posix_mkfifo(__DIR__."/test.fifo", 0644);

echo "made..\n";

$f=fopen(__DIR__."/test.fifo","r+");
//$f=fopen(__DIR__."/test.fifo","a");
//$f=fopen(__DIR__."/test.fifo","r");
echo "opened..\n";
stream_set_blocking($f, false);
$written=fputs($f,"hello");
echo "written: $written\n";
fclose($f);