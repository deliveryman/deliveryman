<?php
namespace Deliveryman\Connection;

/**
 * SSH connectio interface
 * 
 * @author Alexander Sergeychik
 */
interface ConnectionInterface {

	/**
	 * Runs command
	 *
	 * @param string $command - command
	 * @param string|array $arguments - command arguments
	 * @param string $pwd - working directory for command
	 * @param callable $callback - callback for output line processing
	 * @return int - status code
	 */
	public function run($command, $arguments = null, $pwd = null, $callback = null);

	/**
	 * Uploads stream to target file path.
	 * Returns true or throws exception on error.
	 *
	 * @param string|stream $stream        	
	 * @param string $target        	
	 * @return bool
	 * @throws ConnectionException
	 */
	public function upload($stream, $target);

	/**
	 * Downloads file to stream
	 * 
	 * @param string $target - target file path
	 * @param stream|resource $stream - stream to write to
	 */	
	public function download($target, $stream);
	
}