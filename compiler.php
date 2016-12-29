<?php

require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Application;
use Methylbro\Compiler\CompileCommand;
use Methylbro\Compiler\ExtractCommand;

$application = new Application('Compiler', '1.0.0');

$compile = new CompileCommand();
$extract = new ExtractCommand();

$application->add($compile);
//$application->setDefaultCommand($compile->getName());

$application->run();
