<?php
namespace Deliveryman\Log;

/**
 * Logger chain implementation
 *
 * @author Alexander Sergeychik
 */
class ChainLogger implements LoggerInterface, \IteratorAggregate {

	/**
	 * Chained loggers
	 *
	 * @var LoggerInterface[]
	 */
	protected $loggers = array();

	/**
	 * Returns loggers
	 *
	 * @return LoggerInterface[]
	 */
	public function getLoggers() {
		return $this->loggers;
	}

	/**
	 * Adds logger to collection
	 *
	 * @param LoggerInterface $logger        	
	 * @return ChainLogger
	 */
	public function addLogger(LoggerInterface $logger) {
		$this->loggers[] = $logger;
		return $this;
	}

	/**
	 * Removes logger from collection if exists
	 *
	 * @param LoggerInterface $logger        	
	 * @return ChainLogger
	 */
	public function removeLogger(LoggerInterface $logger) {
		if (in_array($logger, $this->loggers)) {
			unset($this->loggers[array_search($logger, $this->loggers)]);
		}
		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getIterator() {
		return new \ArrayIterator($this->getLoggers());
	}

	/**
	 * {@inheritDoc}
	 */
	public function log($level, $message) {
		foreach ($this->loggers as $logger) {
			try {
				call_user_func(array(
					$logger, 
					__METHOD__
				), func_get_args());
			} catch (\Exception $e) {
				// do nothing, just skip failed logger
			}
		}
	}

}
