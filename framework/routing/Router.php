<?php

namespace Framework\routing;

use Framework\Application;
use Framework\helpers\StringHelper;
use Framework\middlewares\AuthMiddleware;
use Framework\middlewares\BaseMiddleware;
use Framework\Request;
use Framework\Response;
use Framework\validation\ValidationException;
use Modules\DD;

class Router
{
    public const DEFAULT_REDIRECT_METHOD = 'get';

    public Request $request;
    public Response $response;
    public static string $redirectMethod = self::DEFAULT_REDIRECT_METHOD;
    protected ?Route $activeRoute = null;
    protected static ?Router $instance = null;
    protected static array $routes = [];

    private static array $rules = [];

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

    public static function getInstance(Request $request, Response $response): Router
    {
        if (is_null(static::$instance)) {
            return static::$instance = new static($request, $response);
        }

        return static::$instance;
    }

    /**
     * @throws \Exception
     */
    public static function get(string $path, $callback): Route
    {
        return static::addNew(__FUNCTION__, $path, $callback);
    }

    /**
     * @throws \Exception
     */
    public static function post(string $path, $callback): Route
    {
        return static::addNew(__FUNCTION__, $path, $callback);
    }

    /* @description is designed for pages like 404, 403, 400, 500...
     * as they must be independent of other routes
     * may be rename it to "errorHandler"?
     */
    public static function addSystem(string $path, \Closure $method): void
    {
        // protected array $errorHandler = []; add to class and save these routes there!
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
        // adds the rules for the list of routes
        $fillInTheSuppliedRoutes();
        static::$rules = [];
//        DD::dd();
    }

    /**
     * @param string $method
     * @param string $path
     * @param $callback
     * e.g. ['accessControls'][$method][$accessName][$route]
     * @throws \Exception
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

    /* TODO to be removed - unused */
    public static function middleware(string $string)
    {

//        return app()->router;
    }

    private static function checkIfRouteAlreadyAdded(string $method, string $path)
    {
        if (isset(static::$routes[$method]) && in_array($path, array_keys(static::$routes[$method]))) {
            $msg = "This Route is already registered! Route: [$path]; METHOD: [$method]";
            throw new \InvalidArgumentException($msg);
        }
    }

    /* @description this is used for named routes mechanic
     */
    public static function route(string $name, array $parameters = []): string
    {
        $methodExistIn = self::routeExistIn($name, 'post');
        $methodExistIn ??= self::routeExistIn($name, 'get');
        self::$redirectMethod = $methodExistIn;
        self::$redirectMethod ??= self::DEFAULT_REDIRECT_METHOD;
        foreach (self::$routes[self::$redirectMethod] as $route) {
            if ($route->name() !== $name) {
                continue;
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
            // URL = http://php-c-framework/products/1/view/
            //DD::dd($replaces); // e.g. $finds = array(2) { [0]=> string(6) "{page}" [1]=> string(7) "{page?}" }
            // e.g. $replaces = array(2) { [0]=> int(2) [1]=> int(2) }

            $path = $route->path;
            // from this '/products/{page}' '{page}' will be replaced with its value
            // from this '/products/{page?}' '{page?}' will be replaced with its value
            $path = str_replace($finds, $replaces, $path);

            if (strpos($path, '{')) {
                throw new \Exception('Wrong view or parameters supplied to router');
            }

            // remove any optional parameters not provided
            $path = preg_replace("#{[^}]+}#", '', $path); // from '/products/2{test}{test?}' to '/products/2'
            // TODO we should think about warning if a required
            // parameter hasn't been provided...
            return $path;
        }
        throw new \InvalidArgumentException('no route with that name "' . $name . '"');
    }

    private static function routeExistIn(string $name, string $method)
    {
        return in_array($name, array_map(fn($el) => $el->name(), self::$routes[$method])) ? $method : null;
    }

    public function resolve()
    {
        $this->setCurrentActiveRoute();
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
    private function setCurrentActiveRoute(): void
    {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();
//        DD::dd($this);
        $callback = $this->match($method, $path) ?? false;

        if ($callback === false) {
            abort(404);
        }
        $this->activeRoute = $callback;
    }

    public static function getActiveRoute(): Route
    {
        return static::$instance->activeRoute;
    }

    /**
     * @return false|mixed
     * @throws \Throwable
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
            return $this->dispatch([$controller, $controller->action], [$this->request, $this->response, $this->activeRoute]);
//            return $this->dispatch([$controller, $controller->action], [$this->request, $this->response]);
        } catch (\Throwable $e) {
            if ($e instanceof ValidationException) {
                $_SESSION['errors'] = $e->getErrors();
                return redirect('back');
            }

            if (isDev()) {
                throw $e;
            }
            return $this->dispatchError(); // TODO check if $e needs to be passed there
        }
    }

    public function dispatch(array $callback, array $params)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException("Invalid callback passed to dispatch");
        }
        return call_user_func_array($callback, $params);
//        return call_user_func($callback, ...$params); // the same
    }

    private function match(string $method, string $path): ?Route
    {
//        DD::dd($this);
        foreach (static::$routes[$method] as $route) {
            if ($route instanceof Route && $route->matches($method, $path)) {
                return $route;
            }
        }
        // or just use the following if the loop is not used
        // return isset(static::$routes[$method]) && isset(static::$routes[$method][$path]) ? static::$routes[$method][$path] : null;
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
//                ->group(basePath('routes/api.php'));
//
//            Route::middleware('web')
//                ->namespace($this->namespace)
//                ->group(basePath('routes/web.php'));
//        });
//    }
}
