<?php

$loader = require __DIR__.'/../vendor/autoload.php';
$container = require __DIR__.'/../src/bootstrap.php';

$container->get('application')->run();
