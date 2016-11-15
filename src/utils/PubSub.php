<?php

namespace wpblockchainaccounts;

/**
 * A channel where a sending party can notify
 * a listening party about events.
 */
class PubSub {

	private $fileName;
	private $socket;
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
	 * Notify listening parties.
	 */
	public function publish($message=TRUE) {
		if (!$message)
			throw new Exception("Can't send falsey data.");

		// If no one is listening, just return.
		if (!file_exists($this->fileName))
			return;

		$socket=socket_create(AF_UNIX,SOCK_STREAM,0);

		$oldcwd=getcwd();
		chdir(dirname($this->fileName));
		$res=socket_connect($socket,basename($this->fileName));
		chdir($oldcwd);
		if (!$res)
			throw new Exception("Can't connect.");

		$encoded=json_encode($message);
		socket_write($socket,$encoded,strlen($encoded));
		socket_close($socket);
	}

	/**
	 * Wait for a notification.
	 */
	public function subscribe() {
		if (file_exists($this->fileName))
			@unlink($this->fileName);

		if (file_exists($this->fileName))
			throw new Exception("There is already a file, but couldn't remove it.");

		$this->socket=socket_create(AF_UNIX,SOCK_STREAM,0);

		$oldcwd=getcwd();
		chdir(dirname($this->fileName));
		$res=socket_bind($this->socket,basename($this->fileName));
		chdir($oldcwd);
		if (!$res)
			throw new Exception("Can't bind socket.");

		$res=socket_listen($this->socket);
		if (!$res)
			throw new Exception("Can't listen to socket.");
	}

	/**
	 * Close.
	 */
	public function close() {
		if ($this->socket) {
			socket_close($this->socket);
			@unlink($this->fileName);
			$this->socket=NULL;
		}
	}

	/**
	 * Wait one tick.
	 */
	public function wait() {
		if (!$this->socket)
			$this->subscribe();

		$r=array($this->socket);
		$w=array();
		$x=array();

		$sel=socket_select($r,$w,$x,$this->timeout);
		if ($sel===FALSE)
			throw new Exception("Can't select on socket.");

		if ($sel) {
			$connection=socket_accept($this->socket);
			$data=socket_read($connection,65536);
			if ($data===FALSE)
				throw new Exception("Can't read from connection.");

			$decoded=json_decode($data,TRUE);
			socket_close($connection);
			socket_close($this->socket);
			@unlink($this->fileName);

			return $decoded;
		}

		socket_close($this->socket);
		@unlink($this->fileName);
		return FALSE;
	}
}