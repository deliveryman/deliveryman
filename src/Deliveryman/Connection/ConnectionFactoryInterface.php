<?php
namespace Deliveryman\Connection;

interface ConnectionFactoryInterface {
	
	/**
	 * Creates connection
	 * 
	 * @return Connection
	 */
	public function create($config);
	
	
}
