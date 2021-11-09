<?php

use Framework\Application;
use Framework\helpers\Config;
use Framework\Logger;
use Framework\Session;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

/* START of ERROR REPORTING */
ini_set('display_errors', '1');
//ini_set('display_startup_errors', '1');
// disabling error reporting
// just show "This page isn’t working php-c-framework is currently unable to handle this request. HTTP ERROR 500"
//ini_set('display_errors', '0');
error_reporting(E_ALL);
/* END of ERROR REPORTING */

require_once __DIR__ . './../vendor/autoload.php';
Session::initIfItDoesNotExist(); // TODO move to more specific place
Config::init();
// System logger
$logger = Logger::getInst();
// System Error Handler
// (new \Framework\ProductionErrorHandler($logger))->register();
//(new ErrorHandler($logger))->register();
// System application

// TODO check if $whoops->pushHandler(new PrettyPageHandler()); can be placed in Router::resolveController method
if (isDev()) {
    $whoops = new Run();
    $whoops->pushHandler(new PrettyPageHandler());
    $whoops->register();
//                throw new \Exception($t->getMessage() . "<br/>" . $t->getFile());
}

//\Modules\DD::dd(Config::get());
Application::create(dirname(__DIR__), Config::get());
$app = Application::getInstance();
//(new \Framework\db\Query())->insert('users', ['email', 'firstname', 'lastname', 'status', 'password',],
//    ['email@gmail.com', 'fadsfasd', 'adsfasdfasdf', '1', 'sdfasdfasdd']);

require_once base_path() . '/app/routes/web.php';

$app->run();
