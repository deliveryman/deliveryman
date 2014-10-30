<?php
namespace Deliveryman\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
class DeployCommand extends Command {
	
	public function configure() {
		$this->setName('deploy');
		$this->setDescription('Deploys application to specified target');
		
		$this->addArgument('target', InputArgument::IS_ARRAY, 'Deployment target');
		
		$this->addOption('dry-run', null, InputOption::VALUE_NONE, 'Just emulate operation without file transfer');
	}
	
}
