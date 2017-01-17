<?php

namespace Methylbro\Compiler;

use Psr\Log\LoggerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DistributionBuilder
{
    private $logger;
    private $steps;

    public function __construct(LoggerInterface $logger = null)
    {
        if (!$logger) {
            $this->logger = new \Psr\Log\NullLogger();
        }

        $this->steps = [
            new \Methylbro\Compiler\Phar\Unlink(),
            new \Methylbro\Compiler\Phar\Factory(),
            new \Methylbro\Compiler\Phar\Stub(),
            new \Methylbro\Compiler\Phar\Buffering(),
        ];
    }

    public function configureOptions(OptionsResolver $resolver)
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
            ],
        ]);
    }

    public function build(array $options = [])
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $options = $resolver->resolve($options);

        $this->logger->notice("Build {$options['distrib']['name']}@{$options['version']}", $options);

        return array_reduce($this->steps, function($phar, $step) use ($options) {
            $phar = $step->execute($phar, $options);
            return $phar;
        });
    }
}
