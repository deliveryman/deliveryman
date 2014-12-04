<?php
namespace Deliveryman\ReleaseManager;

use Deliveryman\ReleaseManager\Generator\GeneratorInterface;
use Deliveryman\ReleaseManager\Generator\TimestampGenerator;
use Deliveryman\ReleaseManager\Filesystem\FilesystemInterface;

/**
 * Default release manager.
 *
 * Manage releases in filesystem as
 *
 * /current <-- release1
 * /releases
 * /release1 --> current
 * /release2
 * /release3
 *
 * @author Alexander Sergeychik
 */
class ReleaseManager implements ReleaseManagerInterface {
	
	const CURRENT_NAME = 'current';
	const RELEASES_NAME = 'releases';

	/**
	 * Base path to releases directory
	 *
	 * @var string
	 */
	protected $basePath;

	/**
	 * Filesystem manager
	 *
	 * @var FilesystemInterface
	 */
	protected $filesystem;

	/**
	 * Name generator
	 *
	 * @var GeneratorInterface
	 */
	protected $generator;

	/**
	 * Constructs release manager
	 *
	 * @param string $basePath        	
	 * @param ConnectionInterface $connection        	
	 * @param GeneratorInterface $generator        	
	 */
	public function __construct($basePath, FilesystemInterface $fs, GeneratorInterface $generator = null) {
		$this->basePath = $basePath;
		$this->filesystem = $fs;
		if ($generator !== null) $this->generator = $generator;
	}

	/**
	 * Returns path to base directory
	 *
	 * @return string
	 */
	public function getBasePath() {
		return $this->basePath;
	}

	/**
	 * Returns path to current directory
	 *
	 * @return string
	 */
	public function getReleasesPath() {
		return $this->getBasePath() . $this->getFilesystem()->getDirectorySeparator() . 'releases';
	}

	/**
	 * Returns path to releases directory
	 *
	 * @return string
	 */
	public function getCurrentPath() {
		return $this->getBasePath() . $this->getFilesystem()->getDirectorySeparator() . 'current';
	}

	/**
	 * Returns assigned filesystem
	 *
	 * @return FilesystemInterface
	 */
	public function getFilesystem() {
		return $this->filesystem;
	}

	/**
	 * Returns name generator
	 *
	 * @return GeneratorInterface
	 */
	public function getGenerator() {
		if (!$this->generator) {
			return new TimestampGenerator();
		}
		return $this->generator;
	}

	/**
	 * {@inheritDoc}
	 */	
	public function setup() {
		$this->getFilesystem()->mkdir($this->getReleasesPath());
		return $this;
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function getReleasePath($release) {
		return $this->getReleasesPath() . $this->getFilesystem()->getDirectorySeparator() . $release;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getReleases() {
		$items = $this->getFilesystem()->ls($this->getReleasesPath());
		return array_values($items);
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function createRelease($release = null) {
		
		if (!$release) {
			$release = $this->getGenerator()->generate($this->getReleases()); 
		}
		
		if ($this->hasRelease($release)) {
			throw new ReleaseManagerException(sprintf('Release "%s" already exists', $release));
		}
		
		$path = $this->getReleasePath($release);
		$this->getFilesystem()->mkdir($path, false);
		
		return $release;
	}

	/**
	 * {@inheritDoc}
	 */
	public function removeRelease($release) {
		if (!$this->hasRelease($release)) {
			throw new ReleaseManagerException(sprintf('Release "%s" not exists', $release));
		}
		
		$path = $this->getReleasePath($release);
		$this->getFilesystem()->rm($path, true);
		
		return $this;
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function hasRelease($release) {
		$path = $this->getReleasePath($release);
		return $this->getFilesystem()->isDir($path);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getCurrentRelease() {
		
		if (!$this->filesystem->isSymlink($this->getCurrentPath())) {
			return null;
		}
		
		$symlinkTarget = $this->filesystem->readlink($this->getCurrentPath());
		
		$releases = $this->getReleases();

		// find matching path		
		foreach ($releases as $release) {
			if ($this->getReleasePath($release) == $symlinkTarget) {
				return $release;
			}			
		}
		
		throw new ReleaseManagerException(sprintf('Current release path "%s" specified in symlink but not exists', $symlinkTarget));
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function setCurrentRelease($release) {
		if (!$this->hasRelease($release)) {
			throw new ReleaseManagerException(sprintf('Release "%s" does not exists', $release));
		}
		
		$this->filesystem->symlink($this->getReleasePath($release), $this->getCurrentPath(), true);
		
		return $this;
	}
	
}
