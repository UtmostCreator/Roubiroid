<?php

use app\controllers\AuthController;
use app\controllers\PermissionController;
use app\controllers\SiteController;
use app\core\Application;
use app\core\middlewares\BaseMiddleware;
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
//DD::dd(realpath(\app\core\PointTo::to('../common/config/', 'config.php')));
$config = require_once '../common/config/config.php';
//DD::dd($config);
// System logger
$logger = \app\core\Logger::getInst();

// System Error Handler
(new \app\core\ErrorHandler($logger))->register();

// System application
Application::create(dirname(__DIR__), $config);
$app = Application::getInstance();
//(new \app\core\db\Query())->insert('users', ['email', 'firstname', 'lastname', 'status', 'password',],
//    ['email@gmail.com', 'fadsfasd', 'adsfasdfasdf', '1', 'sdfasdfasdd']);


User::findOne(1);
// ROUTES
Router::get('/', [SiteController::class, 'home']);
//Router::get('/contact', 'contact'); // render view
Router::get('/contact', [SiteController::class, 'contact']);
// render class method
Router::post('/contact', [SiteController::class, 'contact']);
//Router::post('/contact', [SiteController::class, 'contact']);
Router::get('/clear-persistent-flashes', [SiteController::class, 'clearPersistentFlashes']);
Router::get('/login', [AuthController::class, 'login']);
Router::post('/login', [AuthController::class, 'login']);
Router::get('/register', [AuthController::class, 'register']);
Router::post('/register', [AuthController::class, 'register']);
Router::post('/logout', [AuthController::class, 'logout']);
Router::get('/profile', [AuthController::class, 'profile']);

Router::group(['middleware' => ['role:admin', 'role:user']], function () {
//    Router::get('/contact', [AuthController::class, 'contact']);
    Router::get('/sdfasd', [AuthController::class, 'sdfasd']);
//    Router::get('/sdfasd', [AuthController::class, 'sdfasd']);
});

Router::group(['middleware' => ['role:admin', 'role:user']], function () {
//    Router::post('/contact', [AuthController::class, 'contact']);
    Router::post('/sdfasd', [AuthController::class, 'sdfasd']);
});


Router::get('/create-permissions', [PermissionController::class, 'createPermissions']);

//Router::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});
//Router::getRoutes();

$app->run();


/** TODO
 * or instead of AuthController::class use string 'AuthController'
 * Router::resourse('pathNameWillBeThis', AuthController::class, [
[
// this
'except' => ['edit', 'create'],
// or this
'only' => ['index', 'show']
]
]);*/
