<?php
namespace Deliveryman\Log;

interface LoggerInterface {
	
	const DEBUG = 0;
	const NOTICE = 0;
	const ERROR = 0;
	
	/**
	 * Logs message.
	 * 
	 * @param const $level
	 * @param string $message
	 */
	public function log($level, $message);
	
}
