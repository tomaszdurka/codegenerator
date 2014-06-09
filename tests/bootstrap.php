<?php

/** @var \Composer\Autoload\ClassLoader $autoLoader */
$autoLoader = require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/TestHelper.php';
$autoLoader->addPsr4('CGMocks\\', __DIR__ . '/mocks');

define ('DIR_TESTS', __DIR__ . '/');
