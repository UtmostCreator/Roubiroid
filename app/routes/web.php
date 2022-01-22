<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CreateNewDbConnectionController;
use App\Http\Controllers\CsrfExampleProtectionController;
use App\Http\Controllers\ExampleController;
use App\Http\Controllers\OrderProductController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ShowProductController;
use App\Http\Controllers\SiteController;
use Framework\exceptions\NotFoundException;
use Framework\routing\Router;

// SYSTEM|DEVELOPMENT ROUTES
// TODO add possibility for debuggin outputting the error message
// TODO e.g. if this is called new \InvalidArgumentException('no route with that name "' . $name . '"')
Router::addSystem(500, function () {
    $mainText = 'Please contact your administrator or your developer for further resolution';
    $noteText = "<span class='error-note'> Please include a steps to reproduce this error; The More info you provide the easier it to resolve</span>";
    throw new Exception(sprintf("%s<br>%s", $mainText, $noteText));
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
/* TODO START of tests - TO BE MOVED out */
//Router::get('/', [ExampleController::class, 'homePage']);
Router::get('/', [ExampleController::class, 'showMigrations']);
Router::get('/select-with-join', [ExampleController::class, 'selectWithJoin']);
Router::get('/select-using-array', [ExampleController::class, 'selectUsingArray']);
Router::get('/select-using-string', [ExampleController::class, 'selectUsingString']);
Router::get('/insert-load-test', [ExampleController::class, 'insertLoop']);
Router::get('/insert-wo-obj', [ExampleController::class, 'insertWOObject']);
Router::get('/insert-wo-obj-time', [ExampleController::class, 'insertWOObjectTime']);
Router::get('/insert-old', [ExampleController::class, 'oldInsert']);
Router::get('/select-test', [ExampleController::class, 'selectTest']);
Router::get('/select-test-time', [ExampleController::class, 'selectTestTime']);
Router::get('/count-recs', [ExampleController::class, 'count']);
Router::get('/clear-load-test', [ExampleController::class, 'clearLoadTestTable']);
/* TODO END of tests - TO BE MOVED out */
Router::get('/products/view/{product}', [ShowProductController::class, 'handle'])->name('view-product');
Router::get('/products/order/{product}', [OrderProductController::class, 'handle'])->name('order-product');
Router::get('/new-login', [AuthController::class, 'newLogin'])->name('log-in-user-form');
Router::post('/new-login', [AuthController::class, 'newLogin'])->name('log-in-user');


Router::get('/add-new-user', [ExampleController::class, 'addNewUser']);
Router::get('/find-user-by-id/{id}', [ExampleController::class, 'findUserById']);
Router::get('/update-user-by-id/{id}', [ExampleController::class, 'updateUserById']);
//Router::get('/show-login', [AuthController::class, 'showLoginForm'])->name('show-login-form');
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
// TODO START OF order does matter
// ROUTES for testing:
Router::get('/select-users', [CreateNewDbConnectionController::class, 'handle'])->name('select-users');
Router::get('/csrf/example', [CsrfExampleProtectionController::class, 'handle'])->name('csrf-example');
Router::post('/csrf/example', [CsrfExampleProtectionController::class, 'handle'])->name('csrf-example');


Router::get('/products/list', [SiteController::class, 'listAdvanced'])->name('product-list-adv');
//Router::get('/products-list/{page?}', [ListProductsController::class, 'handle'])->name('list-products');
//Router::get('/register', [RegisterController::class, 'handle'])->name('register-user');
Router::get('/register', [RegisterController::class, 'handle'])->name('register-user');
Router::post('/register', [RegisterController::class, 'handle'])->name('register-user');
//Router::post('/register', [RegisterController::class, 'handle'])->name('register-user');

// TODO END OF order does matter
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
//Router::get('/register', [AuthController::class, 'register']);
//Router::post('/register', [AuthController::class, 'register']);
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