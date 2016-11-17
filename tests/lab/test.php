<?php

error_reporting(E_ALL); ini_set('display_errors', 1);

$f=popen("/usr/bin/mkfifo 2>&1", "r");
$s=fread($f,1000).fread($f,1000).fread($f,1000);

echo $s;