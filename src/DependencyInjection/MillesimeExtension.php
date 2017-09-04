<?php

namespace Millesime\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;

class MillesimeExtension
{
    public function load(ContainerBuilder $container)
    {
        $container->setParameter('distribution.class', 'Millesime\Compilation\DistributionBuilder');
        $container->setParameter('compilation.class', 'Millesime\Compilation\Compilation');
        $container->setParameter('compilation.factory.class', 'Millesime\Compilation\CompilationFactory');
        $container->setParameter('application.class', 'Symfony\Component\Console\Application');
        $container->setParameter('application.name', '@name@');
        $container->setParameter('application.version', '@version@');
        $container->setParameter('application.compile.class', 'Millesime\Command\CompileCommand');


        $container
            ->register('distribution.step.unlink', 'Millesime\Phar\Unlink')
            ->addArgument(new Reference('logger'))
        ;
        $container
            ->register('distribution.step.factory', 'Millesime\Phar\Factory')
            ->addArgument(new Reference('logger'))
        ;
        $container
            ->register('distribution.step.buffering', 'Millesime\Phar\Buffering')
            ->addArgument(new Reference('distribution.step.buffering.finder'))
            ->addArgument(new Reference('logger'))
        ;
        $container
            ->register('distribution.step.stub', 'Millesime\Phar\Stub')
            ->addArgument(new Reference('logger'))
        ;
        $container
            ->register('distribution.step.buffering.finder', 'Millesime\Finder\FinderGenerator')
        ;

        $container
            ->register('distribution.steps', 'Millesime\Compilation\Steps')
            ->addMethodCall('add', [new Reference('distribution.step.unlink')])
            ->addMethodCall('add', [new Reference('distribution.step.factory')])
            ->addMethodCall('add', [new Reference('distribution.step.buffering')])
            ->addMethodCall('add', [new Reference('distribution.step.stub')])
        ;

        $container
            ->register('distribution.builder', '%distribution.class%')
            ->addArgument(new Reference('distribution.steps'))
            ->addArgument(new Reference('logger'))
        ;

        $container
            ->register('project.factory', 'Millesime\Compilation\ProjectFactory')
            ->addArgument('Millesime\Compilation\Project')
            ->addArgument(new Reference('logger'))
        ;

        $container
            ->register('compilation.factory', '%compilation.factory.class%')
            ->addArgument('%compilation.class%')
            ->addArgument(new Reference('logger'))
        ;

        $container
            ->register('compiler', 'Millesime\Compiler')
            ->addArgument(new Reference('project.factory'))
            ->addArgument(new Reference('compilation.factory'))
            ->addArgument(new Reference('distribution.builder'))
        ;



        $container
            ->register('logger', 'Monolog\Logger')
            ->addArgument('%application.name%')

            ->addMethodCall('pushHandler', [new Reference('logger.handler')])
        ;
        $container
            ->register('logger.handler', 'Monolog\Handler\ErrorLogHandler')

            ->addMethodCall('setFormatter', [new Reference('logger.formatter')])
        ;
        $container
            ->register('logger.formatter', 'Monolog\Formatter\LineFormatter')
            ->addArgument("%%message%%")
        ;


        $container
            ->register('finder.generator', 'Millesime\Finder\FinderGenerator')
        ;



        $container
            ->register('application', '%application.class%')
            ->addArgument('%application.name%')
            ->addArgument('%application.version%')

            ->addMethodCall('add', [new Reference('application.compile')])
            ->addMethodCall('add', [new Reference('application.init')])
        ;

        $container
            ->register('application.init', 'Millesime\Command\InitCommand')
            ->addArgument(new Reference('finder.generator'))
        ;

        $container
            ->register('application.compile', '%application.compile.class%')
            ->addArgument(new Reference('compiler'))
            ->addArgument(new Reference('logger.handler'))
        ;

        return $container;
    }
}
