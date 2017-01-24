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
    ->register('distribution', '%distribution.class%')
;

$container
    ->register('compilation.factory', '%compilation.factory.class%')
    ->addArgument('%compilation.class%')
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
    ->addArgument(new Reference('compilation.factory'))
    ->addArgument(new Reference('distribution'))
;

return $container;
