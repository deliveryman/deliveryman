<?php
namespace Deliveryman\Authentication;

/**
 * SSH key authenication credentials
 * 
 * @author Alexander Sergeychik
 */
class SshKeyCredentials implements CredentialsInterface {
	
	/**
	 * Username
	 * 
	 * @var string
	 */
	protected $username;
	
	/**
	 * Private key text
	 * 
	 * @var string
	 */
	protected $key;
	
	/**
	 * Keyphrase
	 * 
	 * @var string
	 */
	protected $keyphrase;
		
	/**
	 * Constructs SSH key credentials object 
	 * 
	 * @param string $username
	 * @param string $key - key text or file path
	 * @param string $keyphrase - key passphrase
	 */
	public function __construct($username, $key, $keyphrase = null) {
		$this->setUsername($username);

		if (is_file($key)) {
			$this->setKeyFile($key);
		} else {
			$this->setKeyText($key);
		}
		
		if ($keyphrase !== null) $this->setKeyphrase($keyphrase);
	}
	
	/**
	 * Returns username associated with key
	 * 
	 * @return the $username
	 */
	public function getUsername() {
		return $this->username;
	}
	
	/**
	 * Sets username
	 * 
	 * @param string $username
	 * @return SshKeyCredentials
	 */
	public function setUsername($username) {
		$this->username = $username;
		return $this;
	}

	/**
	 * Returns key text
	 * 
	 * @return string $key
	 */
	public function getKey() {
		return $this->key;
	}

	/**
	 * Sets key text
	 *
	 * @param string $key
	 */
	public function setKeyText($text) {
		$this->key = $text;
		return $this;
	}
	
	/**
	 * Sets keyfile
	 * 
	 * @param string $path
	 * @throws \InvalidArgumentException
	 * @return SshKeyCredentials
	 */
	public function setKeyFile($path) {
		if (!file_exists($path) || !is_readable($path)) {
			throw new \InvalidArgumentException(sprintf('Key file "%s" is not exists or is not readable', $path));
		}
		$text = file_get_contents($path);
		
		$this->setKeyText($text);
		return $this;
	}
	
	/**
	 * Returns keyphrase
	 * 
	 * @return string $keyphrase
	 */
	public function getKeyphrase() {
		return $this->keyphrase;
	}

	/**
	 * Sets keyphrase
	 * 
	 * @param string $keyphrase
	 * @return SshKeyCredentials
	 */
	public function setKeyphrase($keyphrase) {
		$this->keyphrase = $keyphrase;
		return $this;
	}



	
}
