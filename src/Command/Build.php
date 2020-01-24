<?php

namespace Millesime\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Millesime\Millesime;

class Build extends Command
{
    protected function configure()
    {
        $this
            ->setName('build')
            ->setDescription('Build phar packages.')
            ->setHelp('')

            ->addArgument('source', InputArgument::OPTIONAL, 'source', getcwd())
            ->addArgument('destination', InputArgument::OPTIONAL, 'destination', getcwd())

            ->addOption('no-scripts', null, InputOption::VALUE_NONE, 'Skips the execution of all scripts defined in millesime.json file.')
            ->addOption('passphrase', 'p', InputOption::VALUE_NONE, 'OpenSSL private key passphrase.')
            ->addOption('watch', 'w', InputOption::VALUE_NONE, 'watch')
        ;
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        if ($input->getOption('passphrase')) {
            $question = new Question('Enter pass phrase for private key:');
            $question->setHidden(true);
            $passphrase = $helper->ask($input, $output, $question);
            $input->setOption('passphrase', $passphrase);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        /* @see https://symfony.com/doc/current/components/console/logger.html */
        /* VERBOSITY :
               [warning] warning
          -v   [notice] notice
          -vv  [info] info
          -vvv [debug] debug
        */
        $logger = new ConsoleLogger($output);

        $watch = $input->getOption('watch');
        $source = $input->getArgument('source');
        $destination = $input->getArgument('destination');

        $millesime = new Millesime(
            $logger,
            $input->getOption('passphrase')?:null,
            $input->getOption('no-scripts')
        );

        $previous = null;
        $hash = '';
        $built = false;
        do {
            $built = $hash!==$previous;

            if ($built) {
                $packages = $millesime($source, $destination);
            }

            $hash = $this->calcHash($source, $packages);

            if ($built) {
                $previous = $hash;
            }

        } while($watch);

        return 0;
    }

    private function calcHash($source, $packages)
    {
        $notName = [];
        foreach ($packages as $package) {
            $notName[] = $package->getName();
            $notName[] = $package->getName().'.pubkey';
        }

        $finder = new \Symfony\Component\Finder\Finder();
        $files = $finder->files()->notName($notName)->in($source);

        $ctx = hash_init('md5');

        foreach ($files as $file) {
            hash_update_file($ctx, $file);
        }

        $hash = hash_final($ctx);

        return $hash;
    }
}
