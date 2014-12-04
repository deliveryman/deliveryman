<?php
namespace Deliveryman\ReleaseManager\Generator;

/**
 * Generates release name using unix timestamp
 * 
 * @author Alexander Sergeychik
 */
class TimestampGenerator implements GeneratorInterface {

	/**
	 * {@inheritDoc}
	 */
	public function generate(array $releases) {
		$timestamp = time();
		
		if (in_array($timestamp, $releases)) {
			throw new GeneratorException(sprintf('Release with name "%s" already exists, timestamp can not be used', $timestamp));
		}
		
		return $timestamp;
	}

}