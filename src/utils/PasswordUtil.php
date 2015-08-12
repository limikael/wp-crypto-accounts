<?php

	namespace wpblockchainaccounts;

	use \Exception;

	/**
	 * Ask for command line passwords.
	 */
	class PasswordUtil {

		/**
		 * Ask for password.
		 */
		public static function askPass($prompt="Password") {
			echo $prompt.": ";
			system('stty -echo');
			$password = trim(fgets(STDIN));
			system('stty echo');
			// add a new line since the users CR didn't echo
			echo "\n";

			return $password;
		}
	}