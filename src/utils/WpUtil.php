<?php

namespace wpblockchainaccounts;

if (class_exists("wpblockchainaccounts\\WpUtil"))
	return;

/**
 * Wordpress utils.
 */
class WpUtil {

	/**
	 * Get base path.
	 */
	public static function getWpBasePath() {
		if (php_sapi_name()=="cli")
			$path=$_SERVER["PWD"];

		else
			$path=$_SERVER['SCRIPT_FILENAME'];

		while (1) {
			if (file_exists($path."/wp-load.php"))
				return $path;

			$last=$path;
			$path=dirname($path);

			if ($last==$path)
				throw new \Exception("Not inside a wordpress install.");
		}
	}

	/**
	 * Get path to WordPress bootstrap file.
	 */
	public static function getWpLoadPath() {
		return WpUtil::getWpBasePath()."/wp-load.php";
	}

	/**
	 * Bootstrap WordPress.
	 */
	public function bootstrap() {
		require_once WpUtil::getWpLoadPath();
	}
}
