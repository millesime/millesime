<?php

namespace Millesime\Phar;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Millesime\Compilation\Step;

class Factory implements Step
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger ?: new NullLogger();
    }

    /**
     * {@inheritdoc}
     */
    public function execute(\Phar $phar = null, array $options = array())
    {
        $phar = new \Phar($options['filename']);

        $this->logger->debug("new Phar in {$options['filename']}");

        return $phar;
    }
}
