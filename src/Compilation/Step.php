<?php

namespace Millesime\Compilation;

interface Step
{
    /**
     * @param \Phar $phar
     * @param array $options
     * @return \Phar
     */
    public function execute(\Phar $phar = null, array $options = array());
}
