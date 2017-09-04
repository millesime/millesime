<?php

namespace Millesime\Compilation;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DistributionBuilder
{
    private $logger;
    private $steps;

    public function __construct(Steps $steps, LoggerInterface $logger = null)
    {
        $this->steps = $steps;
        $this->logger = $logger ?: new NullLogger();
    }

    public function build(array $options = [])
    {
        $options = $this
            ->configureOptions(new OptionsResolver())
            ->resolve($options)
        ;

        $filename = $options['distrib']['name'].'.phar';
        $options['filename'] = realpath($options['dest']).DIRECTORY_SEPARATOR.$filename;

        $this->logger->warning("Build {$filename} at version {$options['version']}", $options);

        return $this->steps->apply(null, $options);
    }

    private function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'name' => 'project',
            'version' => 'dev-master',
            'source' => getcwd(),
            'dest' => null,
            'authors' => [],
            'distrib' => [
                'name' => 'project-distrib',
                'stub' => null,
                'autoexec' => true,
                'finder' => [
                    'in' => getcwd(),
                ],
            ],
        ]);

        return $resolver;
    }
}
