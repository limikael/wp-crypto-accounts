<?php

	namespace wpblockchainaccounts;

	/**
	 * Bitcoin utilities.
	 */
	class BitcoinUtil {

		/**
		 * Convert to satoshi.
		 */
		public static function toSatoshi($denomination, $amount) {
			switch (strtolower($denomination)) {
				case "satoshi":
					return $amount;
					break;

				case "bits":
					return $amount*100;
					break;

				case "mbtc":
					return $amount*100000;
					break;

				case "btc":
					return $amount*100000000;
					break;

				default:
					throw new Exception("Unknown denomination: ".$denomination);
					return;
			}
		}

		/**
		 * From satoshi.
		 */
		public static function fromSatoshi($denomination, $amount) {
			switch (strtolower($denomination)) {
				case "satoshi":
					return $amount;
					break;

				case "bits":
					return $amount/100;
					break;

				case "mbtc":
					return $amount/100000;
					break;

				case "btc":
					return $amount/100000000;
					break;

				default:
					throw new Exception("Unknown denomination: ".$denomination);
					return;
			}
		}
	}