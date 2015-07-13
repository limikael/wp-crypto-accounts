<?php

	require_once __DIR__."/src/utils/WpUtil.php";
	require_once __DIR__."/src/controller/ApiController.php";

	use wpblockchainaccounts\WpUtil;
	use wpblockchainaccounts\ApiController;

	require_once WpUtil::getWpLoadPath();

	ApiController::init()->dispatch();
