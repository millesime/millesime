<?php

namespace Millesime;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;

/**
 * Millesime.
 * 
 * @author Thomas Gasc <thomas@gasc.fr>
 */
class Millesime
{
    private ContainerBuilder $container;
    private Release $lastRelease;

    /**
     * Constructor
     *
     * @param LoggerInterface|null $passphrase OpenSSL private key passphrase.
     * @param string|null          $passphrase OpenSSL private key passphrase.
     * @param bool                 $noScripts  Skips the execution of all scripts defined in millesime.json file.
     */
    public function __construct(
        LoggerInterface $logger = null,
        string $passphrase = null,
        bool $noScripts = false
    ) {
        $logger = $logger ?: new NullLogger;

        $this->container = new ContainerBuilder();

        $this->container->set('logger', $logger);

        $this->container->setParameter('passphrase', $passphrase);
        $this->container->setParameter('no-scripts', $noScripts);
        $this->container->setParameter('logger', $logger);

        $this->container->addCompilerPass(new RegisterListenersPass());

        $locator = new FileLocator(__DIR__.'/../config/');
        $loader = new PhpFileLoader($this->container, $locator);
        $loader->load('services.php');

        $this->container->compile();
    }

    /**
     * Runs a project compilation.
     *
     * @param string      $source
     * @param string|null $destination
     *
     * @return Package[]
     */
    public function __invoke(string $source, string $destination = null) : Iterable
    {
        unset($this->lastRelease);

        $loader = $this->container->get('millesime.manifest.loader');
        $compiler = $this->container->get('millesime.compiler');

        $manifest = $loader->fromFile($source);
        $release = new Release($manifest, $source, $destination);
        $packages = $compiler($release);

        $this->lastRelease = $release;

        return $packages;
    }

    public function getLastRelease() : Release
    {
        return $this->lastRelease;
    }
}