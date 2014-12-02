<?php
namespace Deliveryman\Connection;

/**
 * Creates connection from connection configuration
 * 
 * @author Alexander Sergeychik
 */
class ConnectionFactory implements ConnectionFactoryInterface {
	
	/**
	 * Already generated name
	 * 
	 * @var array
	 */
	static protected $names = array();
	
	/**
	 * Generates random name
	 * 
	 * @return string
	 */
	static public function generateConnectionName() {
		do {
			$name = 'connection' . md5(rand(10000,30000) . time());
		} while (!in_array($name, self::$names));
		
		self::$names[] = $name;
		return $name;
	}
	
	/**
	 * {@inheritDoc}
	 */	
	public function create($config) {

		if (!isset($config['host'])) {
			throw new ConnectionFactoryException('Host is not defined in configuration "host" key');
		}
		
		if (!isset($config['user'])) {
			throw new ConnectionFactoryException('Username is not defined in configuration "user" key');
		}
		
		// authentication
		if (isset($config['password'])) {
			$auth = array('password' => $config['password']);
		} elseif (isset($config['ssh_key'])) {
			$auth = array('key' => $config['ssh_key']);
			if (isset($config['ssh_keyphrase'])) {
				$auth['keyphrase'] = $config['ssh_keyphrase'];
			}
		} else {
			throw new ConnectionFactoryException('Nethier password nor SSH key provided in configuration ("password" or "ssh_key"/"ssh_keyphrase" keys)');
		}
		
		$connection = new Connection(self::generateConnectionName(), $config['host'], $config['user'], $auth);
		
		return $connection;
	}
	
}
