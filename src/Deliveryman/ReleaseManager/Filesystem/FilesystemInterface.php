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
	 * @return string - separator char
	 */
	public function getDirectorySeparator();

	/**
	 * Creates directory, throws exception on error
	 *
	 * @param string $path        	
	 * @param boolean $parents
	 *        	- create parents or not
	 * @throws FilesystemException
	 */
	public function createDirecotry($path, $parents = false);

	/**
	 * Removes directory, throws exception on error
	 *
	 * @param string $path        	
	 * @throws FilesystemException
	 */
	public function removeDirectory($path);

	/**
	 * Symlinks source to target, throws exception on error
	 *
	 * @param string $source        	
	 * @param string $target        	
	 * @throws FilesystemException
	 */
	public function createSymlink($source, $target);

	/**
	 * Removes symlink, throws exception on error
	 *
	 * @param string $source        	
	 * @throws FilesystemException
	 */
	public function removeSymlink($source);

}
