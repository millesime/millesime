<?php

define('MILLESIME_DATE', '{release.date}');
define('MILLESIME_VERSION', '{release.version}');

Phar::interceptFileFuncs();
Phar::mungServer(array('REQUEST_URI'));
Phar::webPhar('{package.name}');

require 'phar://{package.name}/{package.stub}';

__HALT_COMPILER();
