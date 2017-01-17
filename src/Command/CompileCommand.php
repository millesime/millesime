<?php

namespace Methylbro\Compiler\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Methylbro\Compiler\Project;
use Methylbro\Compiler\CompilationFactory;
use Methylbro\Compiler\DistributionBuilder;


class CompileCommand extends Command
{
    public function __construct(CompilationFactory $compilation, DistributionBuilder $distribution)
    {
        parent::__construct();

        $this->compilation = $compilation;
        $this->distribution = $distribution;
    }

    protected function configure()
    {
        $this
            ->setName('compile')
            ->setDescription('Compile your project in phar')

            ->addArgument('source', InputArgument::OPTIONAL, 'source of your app', getcwd())
            ->addOption('manifest', 'm', InputOption::VALUE_OPTIONAL, 'Wich manifest file you will use', 'compiler.json')
            ->addOption('dest', 'd', InputOption::VALUE_OPTIONAL, 'destination', getcwd())
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $source = $input->getArgument('source');
        $dest = $input->getOption('dest');
        $manifest = $input->getOption('manifest');

        $this->compilation->create(Project::manifest($source, $dest, $manifest))->run($this->distribution);
    }
}
