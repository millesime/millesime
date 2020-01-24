<?php

namespace Millesime\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class Init extends Command
{
    protected function configure()
    {
        $this
            ->setName('init')
            ->setDescription('Creates a basic millesime.json file.')
            ->setHelp('The init command creates a basic millesime.json file in the current directory.')
            ->addArgument('project', InputArgument::REQUIRED, 'the name of the project')
            ->addArgument('package', InputArgument::REQUIRED, 'the name of your package')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $manifest = 'millesime.json';
        if (!file_exists($manifest)) {
            file_put_contents($manifest, $this->getJson($input));
            return 0;
        } else {
            $output->writeln('The file '.$manifest.' already exists.');
            return 1;
        }
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $project = $input->getArgument('project', ucfirst(basename(getcwd())));
        $question = new Question('Project name: [<info>'.$project.'</info>] ', $project);
        $project = $helper->ask($input, $output, $question);
        $input->setArgument('project', $project);

        $package = strtolower($project).'.phar';
        $question = new Question('Package name: [<info>'.$package.'</info>] ', $package);
        $package = $helper->ask($input, $output, $question);
        $input->setArgument('package', $package);

        $output->writeln($this->getJson($input));
        $question = new ConfirmationQuestion('Do you confirm generation? [<info>yes</info>] ', true);
        if (!$helper->ask($input, $output, $question)) {
            $this->dry_run = true;
        }
    }

    private function getJson($input)
    {
        $package = new \stdClass;
        $package->name = $input->getArgument('package');
        $package->signature = ['algorithm' => 'SHA512'];
        $package->autoexec = false;
        $package->finder = $this->finderGeneric();

        $manifest = new \stdClass;
        $manifest->project = $input->getArgument('project');
        $manifest->packages = [$package];

        return json_encode($manifest, JSON_PRETTY_PRINT);
    }

    private function finderGeneric()
    {
        return [
            'in' => ['.'],
            'name' => ["*.php"],
            'notName' => ["*Test.php"],
            'notPath' => ["#vendor/.*/.*/Tests#", "#vendor/.*/.*/tests#"],
            'exclude' => ["test", "tests", "doc", "Test", "Tests"],
            'ignoreDotFiles' => true,
            'ignoreVCS' => true,
            'followLinks' => false,
            'ignoreUnreadableDirs' => false,
        ];
    }
}
