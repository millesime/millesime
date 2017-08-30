<?php

namespace Millesime\Compiler\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ConfirmationQuestion;

use Millesime\Compiler\Project;
use Millesime\Compiler\CompilationFactory;
use Millesime\Compiler\DistributionBuilder;


class InitCommand extends Command
{
    private $dry_run = false;

    protected function configure()
    {
        $this
            ->setName('init')
            ->setDescription('Initialize a new project')

            ->addArgument('project', InputArgument::REQUIRED, 'the name of the project')
            ->addArgument('distrib', InputArgument::REQUIRED, 'the name of your distrib')

            ->addOption('manifest', 'm', InputOption::VALUE_OPTIONAL, 'Wich manifest file you will use', 'millesime.json')
            ->addOption('force', 'f', InputOption::VALUE_OPTIONAL, 'Force', false)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->dry_run) {
            $manifest = $input->getOption('manifest');
            if (!file_exists($manifest) || $input->getOption('force')) {
                file_put_contents($manifest, $this->getJson($input));
            } else {
                $output->writeln('The file '.$manifest.' already exists.');
            }
        }
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $project = $input->getArgument('project', ucfirst(basename(getcwd())));
        $question = new Question('Project name: [<info>'.$project.'</info>] ', $project);
        $project = $helper->ask($input, $output, $question);
        $input->setArgument('project', $project);

        $distrib = $input->getArgument('project', strtolower($project));;
        $question = new Question('Distribution name: [<info>'.$distrib.'</info>] ', $distrib);
        $distrib = $helper->ask($input, $output, $question);
        $input->setArgument('distrib', $project);

        $output->writeln($this->getJson($input));
        $question = new ConfirmationQuestion('Do you confirm generation? [<info>yes</info>] ', true);
        if (!$helper->ask($input, $output, $question)) {
            $this->dry_run = true;
        }
    }

    private function getJson($input)
    {
        $project = $input->getArgument('project');
        $distrib = $input->getArgument('distrib');

        $dist = new \stdClass;
        $dist->name = $distrib; 

        $config = new \stdClass;
        $config->name = $project;
        $config->distrib = [$dist];

        return json_encode($config, JSON_PRETTY_PRINT);
    }
}
