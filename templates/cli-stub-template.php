#!/usr/bin/env php
<?php

define('MILLESIME_DATE', '{release.date}');
define('MILLESIME_VERSION', '{release.version}');

Phar::interceptFileFuncs();
Phar::mapPhar('{package.name}');

require 'phar://{package.name}/{package.stub}';

__HALT_COMPILER();
