<?php
namespace Deliveryman\ReleaseManager\Filesystem;

/**
 * Filesystem manipulation interface
 *
 * @author Alexander Sergeychik
 */
interface FilesystemInterface {

	/**
	 * Returns filesystem directory separator
	 *
	 * @return string - directory separator char
	 */
	public function getDirectorySeparator();

	/**
	 * Determine if the given path absolute
	 *
	 * @param string $path        	
	 * @return boolean
	 */
	public function isAbsolute($path);

	/**
	 * Determine if path is directory
	 *
	 * @param string $path        	
	 * @param boolean $strict
	 *        	- if true, links are not threat as directory
	 * @return boolean
	 */
	public function isDir($path, $strict = true);

	/**
	 * Determine if path is file
	 *
	 * @param string $path        	
	 * @param boolean $strict
	 *        	- if true, links are not threat as directory
	 * @return boolean
	 */
	public function isFile($path, $strict = true);

	/**
	 * Determine if path is symlink
	 *
	 * @param string $path        	
	 * @return boolean
	 */
	public function isSymlink($path);

	/**
	 * Lists directory contents, returns array of items as [path]=[value]
	 *
	 * @param string $path        	
	 * @return array
	 * @throws FilesystemException
	 */
	public function ls($path);

	/**
	 * Creates directory, throws exception on error
	 *
	 * @param string $path        	
	 * @param boolean $parents
	 *        	- create parents or not
	 * @throws FilesystemException
	 */
	public function mkdir($path, $parents = false);

	/**
	 * Removes path, throws exception on error
	 *
	 * @param string $path        	
	 * @param boolean $recursively
	 *        	- remove directory even it's not empty
	 * @throws FilesystemException
	 */
	public function rm($path, $recursively = false);

	/**
	 * Symlinks source to target, throws exception on error
	 *
	 * @param string $path        	
	 * @param string $target 
	 * @param boolean $overwrite - if set to true - overwrites current symlink       	
	 * @throws FilesystemException
	 */
	public function symlink($path, $target, $overwrite = true);

	/**
	 * Removes symlink, throws exception on error
	 *
	 * @param string $path        	
	 * @throws FilesystemException
	 */
	public function unlink($path);
	
	/**
	 * Reads link contents
	 * 
	 * @param string $path
	 * @return string
	 * @throws FilesystemException
	 */
	public function readlink($path);

}
