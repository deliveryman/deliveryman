<?php
namespace Deliveryman\Connection;

use Illuminate\Remote\Connection as BaseConnection;

/**
 * Connection implementation as fascade for Illuminate Remote.
 * Run and upload/download actions on remote hosts that supports ssh.
 *
 * @author Alexander Sergeychik
 */
class Connection {

	/**
	 * Undelying connection
	 * 
	 * @var BaseConnection
	 */
	protected $connection;

	/**
	 * Constructs connection
	 * 
	 * @param string $name
	 * @param string $host
	 * @param string $username
	 * @param array $auth
	 */	
	public function __construct($name, $host, $username, array $auth) {
		$this->connection = new BaseConnection($name, $host, $username, $auth);
	}

	public function status() {
		return $this->connection->status();
	}

	public function download($remote, $local) {
		try {
			$this->connection->get($remote, $local);
		} catch (\Exception $e) {
			throw new ConnectionException(sprintf('Download failed: %s', $e->getMessage()), null, $e);
		}
		return $this;
	}

	public function upload($local, $remote) {
		try {
			$this->connection->put($local, $remote);
		} catch (\Exception $e) {
			throw new ConnectionException(sprintf('Upload failed: %s', $e->getMessage()), null, $e);
		}
		return $this;
	}

	public function execute($command) {
		try {
			$this->connection->run($command);
		} catch (\Exception $e) {
			throw new ConnectionException(sprintf('Execution failed: %s', $e->getMessage()), null, $e);
		}
		
		return $this;
	}

}
