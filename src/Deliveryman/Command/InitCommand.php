<?php
namespace Deliveryman\Command;

use Symfony\Component\Console\Command\Command;

class InitCommand extends Command {
	
	public function configure() {
		$this->setName('init');
		$this->setDescription('Initializes applicaion');
	}
	
}
