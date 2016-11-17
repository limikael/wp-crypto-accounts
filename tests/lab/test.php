<?php

error_reporting(E_ALL); ini_set('display_errors', 1);

$f=popen("/bin/ls", "r");
$s=fread($f,1000);

echo $s;