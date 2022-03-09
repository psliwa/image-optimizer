<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$loader = require __DIR__ . '/../vendor/autoload.php';

$loader->addPsr4('ImageOptimizer\\', __DIR__ . '/ImageOptimizer');
