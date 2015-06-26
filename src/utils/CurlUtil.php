<?php

	/**
	 * Curl util.
	 */
	class CurlUtil {

		/**
		 * Make a json api request.
		 */
		public static function jsonApiRequest($url, $params=array()) {
			if (sizeof($params)) {
				$a=array();

				foreach ($params as $key=>$value)
					$a[]=$key."=".urlencode($value);

				$url.="?".join("&",$a);
			}

			//Log::debug("curling: ".$url);

			$curl=curl_init($url);
			curl_setopt($curl,CURLOPT_RETURNTRANSFER,TRUE);
			$res=curl_exec($curl);

			return json_decode($res,TRUE);
		}
	}