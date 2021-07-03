<?php

namespace app\core;

use app\common\helpers\ArrayHelper;
use app\common\helpers\StringHelper;
use app\core\exceptions\NotFoundException;
use app\core\middlewares\AuthMiddleware;
use app\core\middlewares\BaseMiddleware;
use modules\DD\DD;

class Router
{
    public Request $request;
    public Response $response;
    private static array $rules = [];
    protected static array $routes = [];

    /**
     * Router constructor.
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public static function get(string $path, array $callback)
    {
        static::addNew('get', $path, $callback);
    }

    public static function post(string $path, array $callback)
    {
        static::addNew('post', $path, $callback);
    }

    public static function addNew(string $method, string $path, array $callback)
    {
        if (!empty(static::$rules)) {
            self::calculateAccessRules($method, $path, $callback);
        } else {
            static::checkIfRouteAlreadyAdded($method, $path);
            static::$routes[$method][$path] = $callback;
        }
    }

    public static function group(array $array, \Closure $fillInTheSuppliedRoutes)
    {
        static::$rules = $array;
        $fillInTheSuppliedRoutes();
        static::$rules = [];
//        DD::dd();
    }

    /**
     * @param string $method
     * @param string $path
     * @param $callback
     * e.g. ['accessControls'][$method][$accessName][$route]
     */
    private static function calculateAccessRules(string $method, string $path, $callback): void
    {
        $allowedAccessNames = ['role', 'permission', 'rules'];
        $defaultMiddleWares = ['auth'];

        if (!is_array(static::$rules['middleware'])) {
            if (in_array(static::$rules['middleware'], $defaultMiddleWares)) {
                if (static::$rules['middleware'] === 'auth') {
                    $auth = new AuthMiddleware();
                    $auth->execute();
                }
                if (static::$rules['middleware'] === 'something else') {
//                    $auth = new BaseMiddleware();
//                    $auth->execute();
                }
                $middlewareClassName = ucfirst(static::$rules['middleware']) . 'Middleware';
                $class = class_exists($middlewareClassName) ? new $middlewareClassName() : null;
                if ($class && is_a($class, BaseMiddleware::class)) {
                    $class->execute();
                }

                return;
            }
        }

        foreach (static::$rules['middleware'] as $rule) {
            $searchedVal = ':';
            $accessName = StringHelper::extractString($rule, $searchedVal);
            $ruleName = StringHelper::extractString($rule, $searchedVal, false);

            if (!in_array($accessName, $allowedAccessNames)) {
                throw new \InvalidArgumentException('Invalid Access Name is specified!');
            }

            static::checkIfRouteAlreadyAdded($path, $method);
            if (
                isset(static::$routes['accessControls'][$method]) &&
                isset(static::$routes['accessControls'][$method][$accessName]) &&
                isset(static::$routes['accessControls'][$method][$accessName][$ruleName]) &&
                in_array($path, static::$routes['accessControls'][$method][$accessName][$ruleName])
            ) {
                $msg = "An attempt to add the same route twice. Route name: [$path]; METHOD: [$method] in GROUP";
                throw new \InvalidArgumentException($msg);
            }
            static::$routes['accessControls'][$method][$accessName][$ruleName][] = $path;
            static::$routes[$method][$path] = $callback;
        }
    }

    public static function middleware(string $string)
    {

        return app()->router;
    }

    private static function checkIfRouteAlreadyAdded(string $method, string $path)
    {
        if (isset(static::$routes[$method]) && in_array($path, array_keys(static::$routes[$method]))) {
            $msg = "This Route is already registered! Route: [$path]; METHOD: [$method]";
            throw new \InvalidArgumentException($msg);
        }
    }

    public function resolve()
    {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();
        $callback = static::$routes[$method][$path] ?? false;
//        $callback = static::$routes['rules'][$method][$path] ?? false;

        if ($callback === false) {
            throw new NotFoundException();
//            return renderView('_404'); // get Not Found controller
        }

        // this is applied to static functions
        if (is_string($callback)) {
            return Application::$app->view->renderView($callback);
        }

        // creates an object to call a non-static method
        if (is_array($callback)) {
            /** @var Controller $controller */
            $controller = new $callback[0]();
            Application::$app->controller = $controller;
            $controller->action = $callback[1];

            /** @var BaseMiddleware $middleware */
            foreach ($controller->getMiddlewares() as $middleware) {
                $middleware->execute();
            }

            $callback[0] = $controller;
        }

        // this is applied to non-static functions
        // params to the method of a controller ($this->request, $this->response)
        // e.g. public function login(Request $request, Response $response)
        return call_user_func($callback, $this->request, $this->response);
    }

    public static function getRoutes()
    {
        DD::dd(Router::$routes);
    }

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
//    public function boot()
//    {
//        $this->configureRateLimiting();
//
//        $this->routes(function () {
//            Route::prefix('api')
//                ->middleware('api')
//                ->namespace($this->namespace)
//                ->group(base_path('routes/api.php'));
//
//            Route::middleware('web')
//                ->namespace($this->namespace)
//                ->group(base_path('routes/web.php'));
//        });
//    }
}
