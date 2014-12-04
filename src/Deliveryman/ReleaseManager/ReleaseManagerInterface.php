<?php
namespace Deliveryman\ReleaseManager;

/**
 * Release manager interface
 * 
 * @author Alexander Sergeychik
 */
interface ReleaseManagerInterface {
	
	const VALIDITY_VALID = true;
	const VALIDITY_INVALID = false;
	const VALIDITY_ANY = null;
	
	/**
	 * Returns releases list
	 * 
	 * @return array - array of release names
	 */
	public function getReleases($validity = self::VALIDITY_ANY);
	
	/**
	 * Returns current release name
	 * 
	 * @return string
	 */	
	public function getCurrentRelease();
	
	/**
	 * Sets specified release as current
	 * 
	 * @param string $release
	 */
	public function setCurrentRelease($release);
	
	/**
	 * Creates release and returns it's name
	 * 
	 * @return string
	 */
	public function createRelease();
	
	
	/**
	 * Returns release path
	 * 
	 * @param string $release
	 * @return string
	 * @throws ReleaseManagerException
	 */
	public function getReleasePath($release);
	
	/**
	 * Removes release
	 * 
	 * @param string $release
	 * @return ReleaseManagerInterface
	 * @throws ReleaseManagerException
	 */
	public function removeRelease($release);
	
	/**
	 * Checks release is valid or not
	 * 
	 * @param string $release
	 * @return boolean
	 */
	public function isReleaseValid($release);
	
	/**
	 * Marks release valid or invalid
	 * 
	 * @param string $release
	 * @param boolean $flag
	 * @return ReleaseManagerInterface
	 * @throws ReleaseManagerException
	 */
	public function markReleaseValid($release, $flag = true);
	
}
