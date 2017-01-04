<?php

namespace Methylbro\Compiler\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Methylbro\Compiler\Compiler;
use Methylbro\Compiler\Project;


class CompileCommand extends Command
{
    private $compiler;

    public function __construct(Compiler $compiler)
    {
        parent::__construct();
        $this->compiler = $compiler;
    }

    protected function configure()
    {
        $path = getcwd();
        $phar = Project::guessPharName($path);

        $this
            ->setName('compile')
            ->setDescription('Compile your project in phar')

            ->addArgument('source', InputArgument::OPTIONAL, 'source of your app', $path)
            ->addArgument('dest', InputArgument::OPTIONAL, 'dest of your phar file', $phar)
            ->addOption('manifest', 'm', InputOption::VALUE_OPTIONAL, 'Wich manifest file you will use', 'compiler.json')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getArgument('source');
        $pharName = $input->getArgument('dest');
        $manifest = $input->getOption('manifest');
        $logger = new \Methylbro\Compiler\Log\OutputLogger($output);

        $project = new Project($this->compiler);
        $this->compiler->setLogger($logger);
        $project->setLogger($logger);

        $project->compile($path, $pharName, $manifest);
    }
}
