<?php

	namespace wpblockchainaccounts;

	use \Exception;

	/**
	 * Make a request using curl.
	 */
	class CurlRequest {

		const NONE="none";
		const JSON="json";

		private $url;
		private $params;
		private $resultProcessing;
		private $result;
		private $mockHandler;

		/**
		 * Constructor.
		 */
		public function __construct($url=NULL) {
			$this->url=$url;
			$this->params=array();
		}

		/**
		 * Set url.
		 */
		public function setUrl($url) {
			$this->url=$url;
		}

		/**
		 * Set mock handler.
		 */
		public function setMockHandler($f) {
			$this->mockHandler=$f;
		}

		/**
		 * Set param.
		 */
		public function setParam($param, $value) {
			$this->params[$param]=$value;

			return $this;
		}

		/**
		 * Set result processing.
		 */
		public function setResultProcessing($processing) {
			$this->resultProcessing=$processing;

			return $this;
		}

		/**
		 * Run.
		 */
		public function exec() {
			if ($this->mockHandler) {
				$f=$this->mockHandler;

				$this->result=$f($this->params);

				return $this->result;
			}

			$url=$this->url;

			if (sizeof($this->params)) {
				$a=array();

				foreach ($this->params as $key=>$value)
					$a[]=$key."=".urlencode($value);

				$joined=join("&",$a);

				if (strpos($url,"?")===FALSE)
					$url.="?".$joined;

				else
					$url.="&".$joined;
			}

			$curl=curl_init($url);
			curl_setopt($curl,CURLOPT_RETURNTRANSFER,TRUE);
			curl_setopt($curl,CURLOPT_FOLLOWLOCATION,TRUE);
			$res=curl_exec($curl);

			if (curl_error($curl))
				throw new Exception(curl_error($curl));

			$code=curl_getinfo($curl,CURLINFO_HTTP_CODE);

			if ($code!=200)
				throw new Exception("HTTP status: ".$code);

			switch ($this->resultProcessing) {
				case CurlRequest::NONE:
					$this->result=$res;
					break;

				case CurlRequest::JSON:
					$decoded=json_decode($res,TRUE);

					if ($decoded===NULL) {
						echo $res;
						throw new Exception("Unable to parse json");
					}

					$this->result=$decoded;
					break;
			}

			return $this->result;
		}

		/**
		 * Get result.
		 */
		public function getResult() {
			return $this->result;
		}
	}