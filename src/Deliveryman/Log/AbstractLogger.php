<?php
namespace Deliveryman\Log;

/**
 * Absract logger class
 *
 * @author Alexander Sergeychik
 */
abstract class AbstractLogger implements LoggerInterface {

	/**
	 * Implementation for logging with additional data parameter for sprintf generation
	 *
	 * {@inheritDoc}
	 */
	public function log($level, $message, array $data = array()) {
		// generate message string
		$sprintfArgs = array_merge(array(
			$message
		), array_values($data));
		$message = call_user_func_array('sprintf', $sprintfArgs);
		
		return $this->write($level, $message, new \DateTime());
	}

	/**
	 * Writes log message
	 *
	 * @param const $level        	
	 * @param string $message        	
	 * @param \DateTime $time        	
	 */
	abstract protected function write($level, $message, \DateTime $time);

}
