<?php

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;

$container = new ContainerBuilder();

$container->setParameter('distribution.class', 'Millesime\Compiler\DistributionBuilder');
$container->setParameter('compilation.class', 'Millesime\Compiler\Compilation');
$container->setParameter('compilation.factory.class', 'Millesime\Compiler\CompilationFactory');
$container->setParameter('application.class', 'Symfony\Component\Console\Application');
$container->setParameter('application.name', '@name@');
$container->setParameter('application.version', '@version@');
$container->setParameter('application.compile.class', 'Millesime\Compiler\Command\CompileCommand');

$container
    ->register('distribution.builder', '%distribution.class%')
;

$container
    ->register('project.factory', 'Millesime\Compiler\ProjectFactory')
    ->addArgument(new Reference('logger'))
;

$container
    ->register('compilation.factory', '%compilation.factory.class%')
    ->addArgument('%compilation.class%')
    ->addArgument(new Reference('logger'))
;

$container
    ->register('compiler', 'Millesime\Compiler\Compiler')
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
    ->register('application', '%application.class%')
    ->addArgument('%application.name%')
    ->addArgument('%application.version%')

    ->addMethodCall('add', [new Reference('application.compile')])
    ->addMethodCall('add', [new Reference('application.init')])
;

$container
    ->register('application.init', 'Millesime\Compiler\Command\InitCommand')
;

$container
    ->register('application.compile', '%application.compile.class%')
    ->addArgument(new Reference('compiler'))
;

return $container;
