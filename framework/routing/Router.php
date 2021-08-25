<?php

namespace App\core\routing;

use App\core\helpers\StringHelper;
use App\core\Application;
use App\core\middlewares\AuthMiddleware;
use App\core\middlewares\BaseMiddleware;
use App\core\Request;
use App\core\Response;
use modules\DD\DD;

class Router
{
    protected Route $activeRoute;
    public Request $request;
    public Response $response;
    public static ?Router $instance = null;
    private static array $rules = [];
    protected static array $routes = [];

    // TODO implement all of these
//    get, post, patch, put, delete, head

    /**
     * Router constructor.
     * @param Request $request
     * @param Response $response
     */
    private function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
//        $this->activeRoute = new Route('', '', new RouteHandler());
    }

    public static function get(string $path, $callback): Route
    {
        $route = static::addNew('get', $path, $callback);
        return $route;
    }

    public static function post(string $path, $callback): Route
    {
        $route = static::addNew('post', $path, $callback);
        return $route;
    }

    public static function addSystem(string $path, \Closure $method): void
    {
        $route = self::$routes[$path] = $method;
        // TODO return system route here
//        return $route;
    }

    /**
     * @throws \Exception
     */
    public static function addNew(string $method, string $path, $callback): Route
    {
        if (!empty(static::$rules)) {
            self::calculateAccessRules($method, $path, $callback);
        } else {
            static::checkIfRouteAlreadyAdded($method, $path);
//            static::$routes[$method][$path] = $callback;
            // controller or view should be processes?
            if (is_array($callback)) {
                static::$routes[$method][$path] = new Route($method, $path, new RouteHandler($callback));
            } elseif (is_callable($callback)) {
                static::$routes[$method][$path] = $callback;
            }
        }
        return static::$routes[$method][$path];
    }

    public static function group(array $array, \Closure $fillInTheSuppliedRoutes): void
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
        // TODO create config for middlewares
        $defaultMiddleWares = ['auth'];

        // TODO check if !is_array is corerct or not
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
                // TODO add to config this postfix [or even get array from config and check if this one exists or not]
                $middlewareClassNamePostfix = 'Middleware';
                $middlewareClassName = ucfirst(static::$rules['middleware']) . $middlewareClassNamePostfix;
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
            static::$routes[$method][$path] = new Route($method, $path, new RouteHandler($callback));
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

    // TODO check if correct.
    public static function getInstance(Request $request, Response $response): Router
    {
        if (is_null(self::$instance)) {
            return new self($request, $response);
        }

        return self::$instance;
    }

    /* @description this is used for named routes mechanic
     */
    public static function route(string $name, array $parameters = []): string
    {
        foreach (self::$routes as $route) {
            if ($route->name() !== $name) {
                throw new \Exception('no route with that name');
            }

            $finds = [];
            $replaces = [];

            foreach ($parameters as $key => $value) {
                // one set for required parameters
                // e.g. '/products/{page}'
                array_push($finds, "{{$key}}");
                array_push($replaces, $value);

                // ... and another for optional parameters
                // e.g. '/products/{page?}'
                array_push($finds, "{{$key}?}");
                array_push($replaces, $value);
            }

            $path = $route->path;
            // from this '/products/{page}' '{page}' will be replaced with its value
            // from this '/products/{page?}' '{page?}' will be replaced with its value
            $path = str_replace($finds, $replaces, $path);

            // remove any optional parameters not provided
            $path = preg_replace("#{[^}]+}#", '', $path);

            // we should think about warning if a required
            // parameter hasn't been provided...
            return $path;
        }
    }

    public function resolve()
    {
        $this->detectAndSetCurrentCallback();
        return $this->resolveController();
    }

    public static function getRoutes()
    {
        return Router::$routes;
        DD::dd(Router::$routes);
    }

    /**
     * @return false|mixed
     */
    private function detectAndSetCurrentCallback(): void
    {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();
        $callback = $this->match($method, $path) ?? false;
//        DD::dd($callback);
        if ($callback === false) {
            abort(404);
//            throw new NotFoundException();
        }
        $this->activeRoute = $callback;
    }

    /**
     * @return false|mixed
     */
    private function resolveController()
    {
        // creates an object to call a non-static method
        if (!$this->activeRoute) {
            abort(500);
        }

        $controller = $this->activeRoute->getActiveController();
        Application::$app->controller = $controller;

        /** @var BaseMiddleware $middleware */
        foreach ($controller->getMiddlewares() as $middleware) {
            $middleware->execute();
        }

        // this is applied to non-static functions
        // params to the method of a controller ($this->request, $this->response)
        // e.g. public function login(Request $request, Response $response)
        // move to route method called dispatch
        try {
            return $this->dispatch([$controller, $controller->action], [$this->request, $this->response]);
        } catch (\Throwable $t) {
            // this action could be thrown and exception
            // so we catch it and display the global error
            // page that we will define in the routes file
            return $this->dispatchError();
        }
    }

    public function dispatch(array $callback, array $params)
    {
        return call_user_func_array($callback, $params);
//        return call_user_func($callback, ...$params); // the same
    }

    private function match(string $method, string $path): ?Route
    {
        foreach (static::$routes[$method] as $route) {
            if ($route instanceof Route && $route->matches($method, $path)) {
                return $route;
            }
        }
        return null;
    }

    public function redirect($path)
    {
        // TODO take more from old project
        header("Location: {$path}", true, 301);
        exit;
    }

    public function errorHandler(int $code, callable $handler)
    {
        static::$routes[$code] = $handler;
    }

    public function dispatchNotAllowed()
    {
        // TODO check if 405 is more suitable
        static::$routes[400] ??= fn() => "not allowed";
        return (static::$routes[400])();
    }

    public function dispatchError()
    {
        static::$routes[500] ??= fn() => "server error";
        return (static::$routes[500])();
    }

    public static function current(): ?Route
    {
        return self::$instance;
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
