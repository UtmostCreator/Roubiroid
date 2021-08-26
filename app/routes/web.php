<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PermissionController;
use Framework\exceptions\NotFoundException;
use Framework\routing\Router;
use App\Http\Controllers\SiteController;
use Framework\PointTo;

// SYSTEM|DEVELOPMENT ROUTES
Router::addSystem(500, function () {
    throw new Exception('server error');
});

Router::addSystem(404, function () {
    throw new NotFoundException();
});

Router::addSystem(400, function () {
    echo 'Bad Request!';
//    throw new NotFoundException();
});

// TODO make possible redirectForeverTo as by default Controller class is required
// can be called using abort
//Router::addSystem('/not-found', fn() => \Framework\Response::redirectForeverTo('/not-found'));


//throw new \Exception('etest');
//$notF = Router::addNew('fasd',$test);

// CLIENT ROUTES
/*
 * TODO check if needed, because it removes array from
 * TODO public static function addNew(string $method, string $path, $callback)
 * */

// HAS BEEN REMOVE [possibility to path str to view]
//Router::get('/', PointTo::getBase() . 'views/_404');

//Router::get('/', [SiteController::class, 'home']);
//Router::get('/contact', [SiteController::class, 'contact']);

//Router::get('/contact', 'contact'); // render view
//Router::get('/products/view/{product}', [SiteController::class, 'viewProduct']);
//Router::get('/products/{id}/view', [SiteController::class, 'viewProduct']);
//Router::get('/products/view/{id}', [SiteController::class, 'viewProductV2']);
//Router::get('/products/view/{id?}', [SiteController::class, 'viewProductV2']);
//Router::get('/products/{page?}', [SiteController::class, 'viewProductV2']);

// examples of named routes
// EXAMPLE:
//Router::get('URL', [ClassController::class, 'method'])->name('route.name.to.refer');
//Router::get('/products/{page?}/{name?}/{text?}', [SiteController::class, 'viewProductV2'])->name('product-list');
Router::get('/products/{page}', [SiteController::class, 'viewProductV2'])->name('product-list');
//Router::route('product-list', ['page' => 2]);
/*Router::get('/products/view/', function () {
    $parameters = Router::current()->parameters();
    \Modules\DD\DD::dd($parameters);
})*/;
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
//    Router::get('/sdfasd', [AuthController::class, 'sdfasd']);
//    Router::get('/sdfasd', [AuthController::class, 'sdfasd']);
});

Router::group(['middleware' => ['role:admin', 'role:user']], function () {
//    Router::post('/contact', [AuthController::class, 'contact']);
//    Router::post('/sdfasd', [AuthController::class, 'sdfasd']);
});


Router::get('/create-permissions', [PermissionController::class, 'createPermissions']);

//Router::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});
//Router::getRoutes();


/** TODO
 * or instead of AuthController::class use string 'AuthController'
 * Router::resourse('pathNameWillBeThis', AuthController::class, [
 * [
 * // this
 * 'except' => ['edit', 'create'],
 * // or this
 * 'only' => ['index', 'show']
 * ]
 * ]);*/