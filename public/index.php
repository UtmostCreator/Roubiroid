<?php

use app\controllers\AuthController;
use app\controllers\SiteController;
use app\core\Application;
use app\core\Router;
use app\models\User;
use modules\DD\DD;

/* ERROR REPORTING */
ini_set('display_errors', '1');
//ini_set('display_startup_errors', '1');
ini_set('display_errors', '0');
error_reporting(E_ALL);
/* ERROR REPORTING */

require_once __DIR__ . './../vendor/autoload.php';
$config = require_once \app\core\PointTo::to('common/config/', 'config.php');

$logger = new \app\core\Logger();

(new \app\core\ErrorHandler($logger))->register();
// ROUTES
$app = new Application(dirname(__DIR__), $config);
Router::get('/', [SiteController::class, 'contact']);
Router::get('/', [SiteController::class, 'home']);
//Router::get('/contact', 'contact'); // render view
Router::get('/contact', [SiteController::class, 'contact']);
// render class method
Router::post('/contact', [SiteController::class, 'contact']);
Router::get('/clear-persistent-flashes', [SiteController::class, 'clearPersistentFlashes']);
Router::get('/login', [AuthController::class, 'login']);
Router::post('/login', [AuthController::class, 'login']);
Router::get('/register', [AuthController::class, 'register']);
Router::post('/register', [AuthController::class, 'register']);
Router::post('/logout', [AuthController::class, 'logout']);
Router::get('/profile', [AuthController::class, 'profile']);
$app->run();
