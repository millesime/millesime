<?php

namespace Millesime\Phar;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Millesime\Compilation\Step;

class Unlink implements Step
{
    private $logger;

    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger ?: new NullLogger();
    }

    public function execute(\Phar $phar = null, array $options = array())
    {
        if (file_exists($options['filename'])) {
            unlink($options['filename']);
            $this->logger->info(sprintf('Removed %s', $options['filename']));
        }

        return $phar;
    }
}
