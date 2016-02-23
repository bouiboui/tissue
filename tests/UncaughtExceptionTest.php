<?php

/**
 * PHPUnit intercepts exceptions so we have to test it in a separate file
 */

include_once dirname(__DIR__) . '/vendor/autoload.php';
use bouiboui\Tissue\Tissue;

Tissue::setConfigPath(dirname(__DIR__) . '/config/config.test.yaml');
Tissue::bindUncaughtExceptionHandler();

throw new \ErrorException('This error was not caught !');
