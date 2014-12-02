<?php
namespace Deliveryman\Log;

/**
 * Classes uses this interface should provide provide logger.
 * 
 * @author Alexander Sergeychik
 */
interface LoggerAwareInterface {
	
	/**
	 * Returns logger instance.
	 * 
	 * @return LoggerInterface
	 */
	public function getLogger();
	
}
