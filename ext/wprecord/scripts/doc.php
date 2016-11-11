#!/usr/bin/env php
<?php

	system(__DIR__."/../vendor/bin/apigen generate -s ".__DIR__."/../SmartRecord.php -d ".__DIR__."/../doc",$ret);
	exit($ret);