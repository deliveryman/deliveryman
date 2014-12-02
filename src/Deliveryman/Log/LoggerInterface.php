<?php
namespace Deliveryman\Log;

/**
 * Logger interface
 * 
 * @author Alexander Sergeychik
 */
interface LoggerInterface {
	
	const DEBUG = 0;
	const NOTICE = 1;
	const WARNING = 2;
	const ERROR = 3;
	const FATAL = 4;

	/**
	 * Logs message.
	 *
	 * @param const $level        	
	 * @param string $message        	
	 */
	public function log($level, $message);

}
