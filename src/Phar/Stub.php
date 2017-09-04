<?php

namespace Millesime\Phar;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Millesime\Compilation\Step;

class Stub implements Step
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
        $pharName = $options['distrib']['name'].'.phar';

        if (array_key_exists('stub', $options['distrib'])) {
            $phar = $this->setStub(
                $phar,
                $pharName,
                $options['distrib']['stub'],
                $options['distrib']['autoexec']
            );
        }

        return $phar;
    }

    /**
     * @param \Phar $phar
     * @param string $pharName
     * @param string $stub
     * @param boolean $autoexec
     * @return \Phar
     */
    public function setStub(\Phar $phar, $pharName, $stub, $autoexec)
    {
        $code =
<<<STUB
<?php
Phar::interceptFileFuncs();
Phar::mapPhar('{$pharName}');
require 'phar://{$pharName}/{$stub}';
__HALT_COMPILER();
STUB;
        if ($autoexec) {
            $code = '#!/usr/bin/env php'.PHP_EOL.$code;
        }

        $phar->setStub($code);

        $this->logger->debug("{$pharName} will run {$stub}");

        return $phar;
    }
}
