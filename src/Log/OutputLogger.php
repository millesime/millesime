<?php

namespace Methylbro\Compiler\Log;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\OutputInterface;

class OutputLogger implements LoggerInterface
{
	private $output;

	public function __construct(OutputInterface $output)
	{
		$this->output = $output;
	}

    public function emergency($message, array $context = array())
    {}

    public function alert($message, array $context = array())
    {}

    public function critical($message, array $context = array())
    {
    	$this->output->writeln('<error>'.$message.'</error>');
    }

    public function error($message, array $context = array())
    {
    	$this->output->writeln('<fg=black;bg=yellow>'.$message.'</>');
    }

    public function warning($message, array $context = array())
    {
    	$this->output->writeln('<info>'.$message.'</info>');
    }

    public function notice($message, array $context = array())
    {
        $this->output->writeln($message);
    }

    public function info($message, array $context = array())
    {
        $this->output->writeln($message, OutputInterface::VERBOSITY_VERBOSE);
    }

    public function debug($message, array $context = array())
    {
    	$this->output->writeln($message, OutputInterface::VERBOSITY_VERY_VERBOSE);
    }

    public function log($level, $message, array $context = array())
    {}
}
