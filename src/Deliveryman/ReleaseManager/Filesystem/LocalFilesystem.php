<?php
namespace Deliveryman\ReleaseManager\Filesystem;

/**
 * Local filesystem implementation
 *
 * @author Alexander Sergeychik
 */
class LocalFilesystem implements FilesystemInterface {

	/**
	 * File mode
	 * 
	 * @var number
	 */
	protected $fileMode = 0777;
	
	/**
	 * Directory mode
	 * 
	 * @var number
	 */
	protected $directoryMode = 0777;

	/**
	 * Constructs local filesystem wrapper
	 * 
	 * @param number $fileMode
	 * @param number $directoryMode
	 */
	public function __construct($fileMode = 0777, $directoryMode = 0777) {
		if ($fileMode !== null) $this->fileMode = $fileMode;
		if ($directoryMode !== null) $this->directoryMode = $directoryMode;
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function getDirectorySeparator() {
		return DIRECTORY_SEPARATOR;
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function isAbsolute($path) {
		if (strspn($path, '/\\', 0, 1) || (strlen($path) > 3 && ctype_alpha($path[0]) && substr($path, 1, 1) === ':' && (strspn($path, '/\\', 2, 1))) || null !== parse_url($path, PHP_URL_SCHEME)) {
			return true;
		}
		return false;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isDir($path, $strict = true) {
		return $strict ? !is_link($path) && is_dir($path) : is_dir($path);
	}

	/**
	 * {@inheritDoc}
	 */
	public function isFile($path, $strict = true) {
		return $strict ? !is_link($path) && is_file($path) : is_file($path);
	}

	/**
	 * {@inheritDoc}
	 */
	public function isSymlink($path) {
		return is_link($path);
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function ls($path) {
		if (!$this->isDir($path, false)) {
			throw new FilesystemException(sprintf('Specified path "%s" is not a directory', $path));
		} elseif (!is_readable($path)) {
			throw new FilesystemException(sprintf('Specified path "%s" is not readable', $path));
		}
		
		$iterator = new \FilesystemIterator($path, \FilesystemIterator::SKIP_DOTS);
		$items = array();
		foreach ($iterator as $file) {
			$items[$file->getPathname()] = $file->getFilename();
		}
		
		return $items;
	}

	/**
	 * {@inheritDoc}
	 */
	public function mkdir($path, $parents = false) {
		if (file_exists($path)) {
			throw new FilesystemException(sprintf('Path "%s" already exists', $path));
		}
		
		$result = @mkdir($path, $this->directoryMode, $parents);
		if (!$result) {
			throw new FilesystemException(sprintf('Unable to create direcotry: %s', error_get_last()));
		}
		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function rm($path, $recursively = false) {
		if ($this->isDir($path, true)) {
			if ($recursively) {
				$iterator = new \FilesystemIterator($path, \FilesystemIterator::SKIP_DOTS);
				foreach ($iterator as $file) {
					$this->rm($file->getPathname(), true);
				}
			} 
			$result = @rmdir($path);
			if (!$result) {
				throw new FilesystemException(sprintf('Unable to rmdir() directory: %s', error_get_last()));
			}
		} elseif ($this->isFile($path, true)) {
			$result = @unlink($path);
			if (!$result) {
				throw new FilesystemException(sprintf('Unable to unlink() file: %s', error_get_last()));
			}
		} elseif ($this->isSymlink($path)) {
			$this->unlink($path);
		} else {
			throw new FilesystemException(sprintf('Unknown type of location "%s"', $path));
		}
		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function symlink($path, $target, $overwrite = true) {
		
		if (is_link($target) && $overwrite) {
			if (!@unlink($target)) {
				throw new FilesystemException(sprintf('Unable to unlink() path "%s": %s', $target, error_get_last()));
			}
		}
		
		$result = symlink($path, $target);
		if (!$result) {
			throw new FilesystemException(sprintf('Unable to symlink() path "%s" to "%s": %s', $path, $target, error_get_last()));
		}
		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function unlink($path) {

		if ($this->isSymlink($path) || $this->isFile($path, true)) {
			$result = @unlink($path);
			if (!$result) {
				throw new FilesystemException(sprintf('Unable to unlink() file: %s', error_get_last()));
			}
		} else {
			throw new FilesystemException('Unable to unlink resource that not a file or symlink');
		}
		
		return $this;
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function readlink($path) {
		$result = @readlink($path);
		if ($result === false) {
			throw new FilesystemException(sprintf('Unable to readlink() resource path "%s"', $path));
		}
		return $result;
	}
	
}
