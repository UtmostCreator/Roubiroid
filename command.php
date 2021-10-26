<?php

require_once __DIR__ . './vendor/autoload.php';
//$config = require_once './config/config.php';
/* START of ERROR REPORTING */
ini_set('display_errors', '1');
error_reporting(E_ALL);
/* END of ERROR REPORTING */

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$console = new \Symfony\Component\Console\Application();
$commands = require __DIR__ . './app/commands.php';

foreach ($commands as $command) {
    $console->add(new $command());
}

try {
    $console->run();
} catch (Exception $e) {
    echo $e->getMessage();
}
