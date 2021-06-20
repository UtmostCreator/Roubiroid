<?php

use app\controllers\AuthController;
use app\controllers\SiteController;
use app\core\Application;
use app\models\User;

/* ERROR REPORTING */
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
/* ERROR REPORTING */

require_once __DIR__ . './../vendor/autoload.php';

// CONFIG
/**
 * @var String CONST SERVER_TYPE this can be either "LOCAL" or "REMOTE"
 */
define("SERVER_TYPE", "LOCAL");
define("REMOTE_PROJECT_LOC", "accounts/system/");

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$config = [
    'website' => [
        'name' => 'Your Web Site Name'
    ],
    'layout' => [
        'value' => 'main',
        'folder' => 'layouts',
    ],
    'views' => [
        'folder' => 'views'
    ],
    'migrations' => [
        'folder' => 'migrations'
    ],
    'userClass' => User::class,
    'db' => [
        'dsn' => $_ENV['DB_DSN'],
        'user' => $_ENV['DB_USER'],
        'password' => $_ENV['DB_PASSWORD'],
    ]
];
$app = new Application(dirname(__DIR__), $config);
$app->router->get('/', [SiteController::class, 'home']);
//$app->router->get('/contact', 'contact'); // render view
$app->router->get('/contact', [SiteController::class, 'contact']);
// render class method
$app->router->post('/contact', [SiteController::class, 'contact']);
$app->router->get('/clear-persistent-flashes', [SiteController::class, 'clearPersistentFlashes']);
$app->router->get('/login', [AuthController::class, 'login']);
$app->router->post('/login', [AuthController::class, 'login']);
$app->router->get('/register', [AuthController::class, 'register']);
$app->router->post('/register', [AuthController::class, 'register']);
$app->router->post('/logout', [AuthController::class, 'logout']);
$app->router->get('/profile', [AuthController::class, 'profile']);
$app->run();
