<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Console\Application;
use Symfony\Component\Finder\Finder;

$package = json_decode(file_get_contents(__DIR__ . '/../composer.json'), true);


$application = new Application('Deliveryman', $package['version']);

// bootstrap commands
$finder = new Finder();
$commands = $finder->files()->name('*Command.php')->in(__DIR__ . '/../src/Deliveryman/Command');
foreach ($commands as $command) {
	$class = 'Deliveryman\\Command\\' . $command->getBasename('.php');
	$command = new $class();
	$application->add($command);
}

$application->run();