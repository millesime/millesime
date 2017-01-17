<?php

namespace Methylbro\Compiler\Phar;

class Stub
{
    public function __construct()
    {}

    public function execute($phar, array $options)
    {
        $pharName = $options['distrib']['name'].'.phar';

        if ($options['distrib']['stub']) {
            $phar = $this->setStub($phar, $pharName, $options['distrib']['stub'], $options['distrib']['autoexec']);
        }

        return $phar;
    }

    public function setStub($phar, $pharName, $stub, $autoexec)
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

        return $phar;
    }
}
