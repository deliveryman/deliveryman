<?php
namespace Deliveryman\Log;

/**
 * Logger that writes nothing
 *
 * @author Alexander Sergeychik
 */
class NullLogger implements LoggerInterface {

	/**
	 * {@inheritDoc}
	 */
	public function log($level, $message) {
		return;
	}

}
