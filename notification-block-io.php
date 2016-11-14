<?php

require_once __DIR__."/src/utils/WpUtil.php";
require_once __DIR__."/src/controller/BlockIoController.php";

use wpblockchainaccounts\WpUtil;
use wpblockchainaccounts\BlockIoController;

WpUtil::bootstrap();

BlockIoController::instance()->processPost();
