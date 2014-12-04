<?php
namespace Deliveryman\ReleaseManager\Filesystem;

interface FilesystemInterface {
	
	/**
	 * Returns filesystem directory separator
	 * 
	 * @return string - separator char
	 */
	public function getDirectorySeparator();
	
	
	public function createDirecotry($path, $parents = false);
	
	public function removeDirectory($path);
	
	public function createSymlink($source, $target);

	public function removeSymlink($source);
	
}
