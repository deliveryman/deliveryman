<?php
namespace Deliveryman\Source;

/**
 * Archive source
 * 
 * @author Alexander Sergeychik
 */
class ArchiveSource implements SourceInterface {
	
	/**
	 * Archive path
	 * 
	 * @var string
	 */
	protected $path;
	
	/**
	 * Constructs path
	 * 
	 * @param string $path
	 */
	public function __construct($path) {
		$this->setPath($path);		
	}
	
	/**
	 * Returns archive path
	 * 
	 * @return string
	 */
	public function getPath() {
		return $this->path;
	}

	/**
	 * Sets path
	 * 
	 * @param string $path
	 * @return ArchiveSource
	 */
	public function setPath($path) {
		$this->path = $path;
		return $this;
	}
	
}