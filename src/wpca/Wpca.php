<?php

	require_once __DIR__."/../utils/CurlRequest.php";

	/**
	 * Command line tool to handle scheduled withdrawals.
	 */
	class Wpca {

		private $url;

		/**
		 * Constructor.
		 */
		public function __construct() {

		}

		/**
		 * Set url for backend.
		 */
		public function setUrl($url) {
			$this->url=$url;
		}

		/**
		 * Status.
		 */
		public function status() {
			$r=new CurlRequest($this->url);
			$r->setResultProcessing(CurlRequest::JSON);
			$r->perform();
		}
	}