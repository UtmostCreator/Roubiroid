<?php

use Framework\Application;
use Framework\helpers\Config;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

require_once __DIR__ . './../vendor/autoload.php';
Config::init();
// TODO check if $whoops->pushHandler(new PrettyPageHandler()); can be placed in Router::resolveController method
if (isDev()) {
    /* START of ERROR REPORTING */
    // just show "This page isn’t working php-c-framework is currently unable to handle this request. HTTP ERROR 500"
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
    /* END of ERROR REPORTING */
    $whoops = new Run();
    $whoops->pushHandler(new PrettyPageHandler());
    $whoops->register();
} elseif (isProd()) {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(0);
}

//\Modules\DD::dd(Config::get());
Application::create();
$app = Application::getInstance();
//$app->bind('paths.base', fn() => __DIR__ . '/..');
// TODO fix with the ENV var
$app->bind('paths.base', fn() => \Framework\Paths::getBase());
//\Modules\DD::dd(app('paths.base'));
//(new \Framework\db\Query())->insert('users', ['email', 'firstname', 'lastname', 'status', 'password',],
//    ['email@gmail.com', 'fadsfasd', 'adsfasdfasdf', '1', 'sdfasdfasdd']);

// TODO mull over on a better way to add it
require_once basePath() . '/app/routes/web.php';

$app->run();
