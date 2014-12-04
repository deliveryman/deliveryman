<?php
namespace Deliveryman\Authentication;

/**
 * Username/password credentials object
 * 
 * @author Alexander Sergeychik
 */
class UsernamePasswordCredentials implements CredentialsInterface {

	/**
	 * Username
	 *
	 * @var string
	 */
	protected $username;

	/**
	 * Password
	 *
	 * @var string
	 */
	protected $password;

	/**
	 * Constructs username/password credentials object
	 *
	 * @param string $username        	
	 * @param string $password        	
	 */
	public function __construct($username, $password) {
		$this->setUsername($username);
		$this->setPassword($password);
	}

	/**
	 * Returns username
	 *
	 * @return string
	 */
	public function getUsername() {
		return $this->username;
	}

	/**
	 * Sets username
	 *
	 * @param string $username        	
	 * @return UsernamePasswordCredentials
	 */
	public function setUsername($username) {
		$this->username = $username;
		return $this;
	}

	/**
	 * Returns password
	 *
	 * @return string
	 */
	public function getPassword() {
		return $this->password;
	}

	/**
	 * Sets password
	 *
	 * @param string $password        	
	 * @return UsernamePasswordCredentials
	 */
	public function setPassword($password) {
		$this->password = $password;
		return $this;
	}

}
