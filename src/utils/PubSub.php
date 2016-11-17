<?php

namespace wpblockchainaccounts;

use \Exception;

/**
 * A channel where a sending party can notify
 * a listening party about events.
 */
class PubSub {

	private $fileName;
	private $subscribeFile;
	private $timeout;

	/**
	 * Construct.
	 */
	public function __construct($fn) {
		$this->fileName=$fn;
		$this->timeout=30;
	}

	/**
	 * Get filename.
	 */
	public function getFileName() {
		return $this->fileName;
	}

	/**
	 * Set timeout.
	 */
	public function setTimeout($timeout) {
		$this->timeout=$timeout;
	}

	/**
	 * Ensure the fifo is created.
	 */
	private function ensureFifo() {
		if (file_exists($this->fileName)) {
			$type=filetype($this->fileName);
			if ($type!="fifo")
				throw new Exception("File already exists, but it is not a fifo");

			return;
		}

		if (function_exists("posix_mkfifo")) {
			if (!posix_mkfifo($this->fileName, 0644))
				throw new Exception("Unable to create fifo using posix_mkfifo");

			return;
		}

		exec("/usr/bin/mkfifo ".escapeshellarg($this->fileName),$ret,$err);
		if ($err)
			throw new Exception("Unable to create fifo using exec(mkfifo)");
	}

	/**
	 * Notify listening parties.
	 */
	public function publish($message=TRUE) {
		$this->ensureFifo();
		$f=fopen($this->fileName,"r+");
		stream_set_blocking($f, false);
		$written=fputs($f,strval($message));
		fclose($f);
	}

	/**
	 * Wait for a notification.
	 */
	public function subscribe() {
		$this->ensureFifo();
		$this->subscribeFile=fopen($this->fileName,"rn");
	}

	/**
	 * Close.
	 */
	public function close() {
		if ($this->subscribeFile) {
			fclose($this->subscribeFile);
			$this->subscribeFile=NULL;
		}
	}

	/**
	 * Wait one tick.
	 */
	public function wait() {
		if (!$this->subscribeFile)
			$this->subscribe();

		$r=array($this->subscribeFile);
		$w=array();
		$x=array();

		$sel=stream_select($r,$w,$x,$this->timeout);
		if ($sel===FALSE)
			throw new Exception("Can't select on file.");

		$s=fgets($this->subscribeFile);
		$this->close();

		return $s;
	}
}