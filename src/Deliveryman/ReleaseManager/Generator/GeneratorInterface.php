<?php
namespace Deliveryman\ReleaseManager\Generator;

/**
 * Interface for release name generation
 * 
 * @author Alexander Sergeychik
 */
interface GeneratorInterface {
	
	/**
	 * Generates release name
	 * 
	 * @param array $releases - currently available releases
	 * @return string
	 * @throws GeneratorException
	 */
	public function generate(array $releases);
	
}
