<?php

$f=popen("/bin/ls", "r");
$s=fread($f,1000);

echo $s;