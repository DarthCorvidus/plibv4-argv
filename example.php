#!/usr/bin/env php
<?php
/**
 * @copyright (c) 2021, Claus-Christoph Küthe
 * @author Claus-Christoph Küthe <floss@vm01.telton.de>
 * @license LGPL
 */

/**
 * A simple example demonstrating Argv.
 */
require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/src/ArgModel.php';
require_once __DIR__.'/src/ArgvModel.php';
require_once __DIR__.'/src/ArgvGeneric.php';
require_once __DIR__.'/src/ArgGeneric.php';
require_once __DIR__.'/src/ArgvException.php';
require_once __DIR__.'/src/Argv.php';
require_once __DIR__.'/src/ArgvReference.php';
require_once __DIR__.'/example/Main.php';
require_once __DIR__.'/example/ArgvExample.php';

$main = new Main($argv);
$main->run();