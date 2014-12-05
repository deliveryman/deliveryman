<?php
namespace Deliveryman\Connection;

use Illuminate\Remote\Connection as BaseConnection;
use Deliveryman\Log\LoggerAwareInterface;
use Deliveryman\Log\NullLogger;
use Deliveryman\Log\LoggerInterface;
use Deliveryman\Authentication\CredentialsInterface;
use Deliveryman\Authentication\SshKeyCredentials;
use Deliveryman\Authentication\UsernamePasswordCredentials;

/**
 * Connection implementation as fascade for Illuminate Remote.
 * Run and upload/download actions on remote hosts that supports ssh.
 *
 * @author Alexander Sergeychik
 */
class Connection implements ConnectionInterface, LoggerAwareInterface {

	/**
	 * Already generated name
	 *
	 * @var array
	 */
	static protected $connectionNames = array();
	
	/**
	 * Generates random name
	 *
	 * @return string
	*/
	static public function generateConnectionName() {
		do {
			$name = 'connection' . md5(rand(10000,30000) . time());
		} while (!in_array($name, self::$connectionNames));
	
		self::$connectionNames[] = $name;
		return $name;
	}
	
	/**
	 * Undelying connection
	 * 
	 * @var BaseConnection
	 */
	protected $gateway;
	
	/**
	 * Logger
	 * 
	 * @var LoggerInterface
	 */
	protected $logger;
	
	/**
	 * Constructs connection
	 * 
	 * @param string $host
	 * @param array $credentials
	 * @param LoggerInterface $logger
	 */	
	public function __construct($host, CredentialsInterface $credentials, LoggerInterface $logger = null) {
		
		$name = self::generateConnectionName();
		if ($credentials instanceof SshKeyCredentials) {
			$connection = new BaseConnection($name, $host, $credentials->getUsername(), array(
				'key' => $credentials->getKey(),
				'keyphrase' => $credentials->getKeyphrase()
			));
			
		} elseif ($credentials instanceof UsernamePasswordCredentials) {
			$connection = new BaseConnection($name, $host, $credentials->getUsername(), array(
				'password' => $credentials->getPassword()
			));
		} else {
			throw new ConnectionException(sprintf('Connection credentials type "%s" is not supported', get_class($credentials)));
		}
		
		$this->gateway = $connection;
		
		if ($logger !== null) $this->setLogger($logger);
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function getLogger() {
		if (!$this->logger) {
			$this->setLogger(new NullLogger());
		}
		return $this->logger;
	}
	
	/**
	 * Sets logger
	 * 
	 * @param LoggerInterface $logger
	 * @return Connection
	 */
	public function setLogger(LoggerInterface $logger) {
		$this->logger = $logger;
		return $this;
	}
	
	public function download($remote, $local) {
		$logger = $this->getLogger();
		$logger->log(LoggerInterface::NOTICE, 'Downloading %s to local %s', array($remote, $local));
		try {
			$this->gateway->get($remote, $local);
		} catch (\Exception $e) {
			throw new ConnectionException(sprintf('Download failed: %s', $e->getMessage()), null, $e);
		}
		return $this;
	}

	public function upload($local, $remote) {
		$logger = $this->getLogger();
		$logger->log(LoggerInterface::NOTICE, 'Uploading %s to remote %s', array($local, $remote));
		try {
			$this->gateway->put($local, $remote);
		} catch (\Exception $e) {
			throw new ConnectionException(sprintf('Upload failed: %s', $e->getMessage()), null, $e);
		}
		return $this;
	}

	public function run($command, $arguments = null, $pwd = null, $callback = null) {
		$logger = $this->getLogger();
		
		$arguments = array_map('escapeshellarg', (array)$arguments);
		$executionCommand = $command . ' ' . implode(' ', $arguments);
		
		$logger->log(LoggerInterface::NOTICE, 'Executing "%s" on remote host', array($executionCommand));
		
		try {
			$this->gateway->run($executionCommand, function($line) use ($logger, &$callback) {
				$logger->log(LoggerInterface::DEBUG, ' - %s', array($line));
				if ($callback) call_user_func($callback, $line);
			});
		} catch (\Exception $e) {
			throw new ConnectionException(sprintf('Execution failed: %s', $e->getMessage()), null, $e);
		}
		
		return $this;
	}

}
