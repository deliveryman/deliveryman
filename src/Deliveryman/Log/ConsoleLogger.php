<?php
namespace Deliveryman\Log;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Logs messages into Symfony console output
 * 
 * @author Alexander Sergeychik
 */
class ConsoleLogger extends AbstractLogger {
	
	/**
	 * @var OutputInterface
	 */
	protected $output;
	
	/**
	 * Constructs console logger
	 * 
	 * @param OutputInterface $output
	 */
	public function __construct(OutputInterface $output) {
		$this->output = $output;
	}
	
	/**
	 * Returns output instance
	 * 
	 * @return OutputInterface
	 */
	public function getOutput() {
		return $this->output;
	}
	
	/**
	 * {@inheritDoc}
	 */
	protected function write($level, $message, \DateTime $time) {
		$this->getOutput()->writeln($message);
	}
	
}
