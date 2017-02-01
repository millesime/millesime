<?php

namespace Millesime\Compiler\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Millesime\Compiler\Project;
use Millesime\Compiler\CompilationFactory;
use Millesime\Compiler\DistributionBuilder;


class CompileCommand extends Command
{
    /**
     * @var CompilationFactory
     */
    private $compilation;

    /**
     * @var DistributionBuilder
     */
    private $distribution;

    public function __construct(CompilationFactory $compilation, DistributionBuilder $distribution)
    {
        parent::__construct();

        $this->compilation  = $compilation;
        $this->distribution = $distribution;
    }

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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $source   = $input->getArgument('source');
        $dest     = $input->getOption('dest');
        $manifest = $input->getOption('manifest');

        $this->compilation->create(Project::manifest($source, $dest, $manifest))->run($this->distribution);
        
        $output->writeln('<info>Compilation completed</info>');
    }
}
