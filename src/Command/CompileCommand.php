<?php

namespace Millesime\Command;

use Monolog\Logger;
use Monolog\Handler\HandlerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Millesime\Compiler;

class CompileCommand extends Command
{
    /**
     * @var Compiler
     */
    private $compiler;

    /**
     * @var HandlerInterface
     */
    private $handler;

    /**
     * @var Array[int]
     */
    private $levels;

    /**
     * @param Compiler $compiler
     * @param HandlerInterface $handler
     */
    public function __construct(Compiler $compiler, HandlerInterface $handler)
    {
        parent::__construct();

        $this->compiler = $compiler;
        $this->handler = $handler;
        $this->levels = [
            OutputInterface::VERBOSITY_QUIET => null,
            OutputInterface::VERBOSITY_NORMAL => Logger::ERROR,
            OutputInterface::VERBOSITY_VERBOSE => Logger::WARNING,
            OutputInterface::VERBOSITY_VERY_VERBOSE => Logger::INFO,
            OutputInterface::VERBOSITY_DEBUG => Logger::DEBUG,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('compile')
            ->setDescription('Compile your project in phar')
            ->addArgument('source', InputArgument::OPTIONAL, 'source of your app', getcwd())
            ->addOption('manifest', 'm', InputOption::VALUE_OPTIONAL, 'Wich manifest file you will use', 'millesime.json')
            ->addOption('dest', 'd', InputOption::VALUE_OPTIONAL, 'destination', getcwd())
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->handler->setLevel(
            $this->levels[$output->getVerbosity()]
        );

        $this->compiler->execute(
            $input->getArgument('source'),
            $input->getOption('dest'),
            $input->getOption('manifest')
        );

        $output->writeln(
            'Compilation completed',
            OutputInterface::VERBOSITY_VERBOSE
        );
    }
}
