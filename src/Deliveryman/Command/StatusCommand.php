<?php
namespace Deliveryman\Command;

use Symfony\Component\Console\Command\Command;
class StatusCommand extends Command {
	
	public function configure() {
		$this->setName('status');
		$this->setDescription('Provides information about current release status');
	}
	
}
