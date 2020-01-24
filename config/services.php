<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return function(ContainerConfigurator $configurator) {

    $services = $configurator->services();

    $services->set('logger')->synthetic();
    $services->set('event_dispatcher', 'Symfony\Component\EventDispatcher\EventDispatcher');

    $services->set('millesime.process_factory', 'Millesime\Factory\ProcessFactory');
    $services->set('millesime.phar_factory', 'Millesime\Factory\PharFactory')
        ->args([ref('event_dispatcher'), ref('logger')])
    ;
    $services->set('millesime.package_factory', 'Millesime\Factory\PackageFactory')
        ->args([ref('event_dispatcher'), ref('logger')])
    ;

    $services->set('millesime.compiler', 'Millesime\Compiler')
        ->args([ref('millesime.phar_factory'), ref('millesime.package_factory'), ref('logger')])
    ;

    /* Build steps */
    $services->set('millesime.created_phar.scripts', 'Millesime\Build\Scripts')
        ->args([ref('millesime.process_factory'), '%no-scripts%', ref('logger')])
        ->tag('kernel.event_listener', ['event' => 'millesime.created_phar'])
    ;
    $services->set('millesime.created_phar.files', 'Millesime\Build\Files')
        ->args([ref('logger')])
        ->tag('kernel.event_listener', ['event' => 'millesime.created_phar'])
    ;
    $services->set('millesime.created_phar.stub.finder', 'Symfony\Component\Finder\Finder');
    $services->set('millesime.created_phar.stub', 'Millesime\Build\Stub')
        ->args([ref('millesime.created_phar.stub.finder'), ref('logger')])
        ->tag('kernel.event_listener', ['event' => 'millesime.created_phar'])
    ;
    $services->set('millesime.created_phar.metadata', 'Millesime\Build\Metadata')
        ->args([ref('logger')])
        ->tag('kernel.event_listener', ['event' => 'millesime.created_phar'])
    ;
    $services->set('millesime.created_phar.sign', 'Millesime\Build\Sign')
        ->args(['%passphrase%', ref('logger')])
        ->tag('kernel.event_listener', ['event' => 'millesime.created_phar'])
    ;

    /* Manifest */
    $services->set('millesime.manifest.hydrator', 'Millesime\Manifest\Hydrator');
    $services->set('millesime.manifest.loader.finder', 'Symfony\Component\Finder\Finder');
    $services->set('millesime.manifest.loader', 'Millesime\Manifest\Loader')
        ->args([ref('millesime.manifest.loader.finder'), ref('millesime.manifest.hydrator'), ref('millesime.process_factory')])
    ;
};