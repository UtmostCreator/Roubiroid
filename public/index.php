<?php

use Framework\Application;
use Framework\ErrorHandler;
use Framework\Logger;
use models\User;

/* START of ERROR REPORTING */
ini_set('display_errors', '1');
//ini_set('display_startup_errors', '1');
ini_set('display_errors', '0');
error_reporting(E_ALL);
/* END of ERROR REPORTING */

require_once __DIR__ . './../vendor/autoload.php';
$config = require_once '../config/config.php';

// System logger
$logger = Logger::getInst();
// System Error Handler
// (new \Framework\ProductionErrorHandler($logger))->register();
(new ErrorHandler($logger))->register();
// System application
Application::create(dirname(__DIR__), $config);
$app = Application::getInstance();
//(new \Framework\db\Query())->insert('users', ['email', 'firstname', 'lastname', 'status', 'password',],
//    ['email@gmail.com', 'fadsfasd', 'adsfasdfasdf', '1', 'sdfasdfasdd']);

require_once Application::$PUBLIC . '/../app/routes/web.php';

$app->run();
