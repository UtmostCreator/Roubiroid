<?php

namespace app\core;

use app\core\exceptions\NotFoundException;
use app\core\middlewares\BaseMiddleware;
use Illuminate\Support\Facades\Route;
use modules\DD\DD;

class Router
{
    public Request $request;
    public Response $response;
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

    public static function get(string $path, $callback)
    {
        static::$routes['get'][$path] = $callback;
    }

    public static function post(string $path, $callback)
    {
        static::$routes['post'][$path] = $callback;
    }

    public function resolve()
    {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();
        $callback = static::$routes[$method][$path] ?? false;

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
