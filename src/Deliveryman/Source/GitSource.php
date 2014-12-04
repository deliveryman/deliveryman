<?php
namespace Deliveryman\Source;

use Deliveryman\Authentication\CredentialsInterface;

/**
 * Git source
 *
 * @author Alexander Sergeychik
 */
class GitSource implements SourceInterface {

	/**
	 * Repository path or URL
	 *
	 * @var string
	 */
	protected $repository;

	/**
	 * Branch
	 *
	 * @var string
	 */
	protected $branch = 'master';

	/**
	 * Credentials bag
	 *
	 * @var CredentialsInterface
	 */
	protected $credentials;

	/**
	 * Constructs git source object
	 *
	 * @param string $repository        	
	 * @param string $branch        	
	 * @param CredentialsInterface $credentials        	
	 */
	public function __construct($repository, $branch = null, CredentialsInterface $credentials = null) {

		$this->setRepository($repository);
		
		if ($branch !== null) $this->setBranch($branch);
		if ($credentials !== null) $this->setCredentials($credentials);
	}

	/**
	 * Returns repository
	 *
	 * @return string $repository
	 */
	public function getRepository() {
		return $this->repository;
	}

	/**
	 * Sets repository
	 *
	 * @param string $repository        	
	 * @return GitSource
	 */
	public function setRepository($repository) {
		$this->repository = $repository;
	}

	/**
	 * Returns branch
	 *
	 * @return the $branch
	 */
	public function getBranch() {
		return $this->branch;
	}

	/**
	 * Sets branch
	 *
	 * @param string $branch        	
	 * @return GitSource
	 */
	public function setBranch($branch) {
		$this->branch = $branch;
	}

	/**
	 * Returns credentials
	 *
	 * @return CredentialsInterface $credentials
	 */
	public function getCredentials() {
		return $this->credentials;
	}

	/**
	 * Sets credentials
	 *
	 * @param CredentialsInterface $credentials        	
	 * @return GitSource
	 */
	public function setCredentials($credentials) {
		$this->credentials = $credentials;
	}

}
