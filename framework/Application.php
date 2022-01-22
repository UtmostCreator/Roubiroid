<?php

namespace Framework;

use Dotenv\Dotenv;
use Framework\authentication\AuthManager;
use Framework\authentication\InterfaceAuthBase;
use Framework\db\Database;
use Framework\helpers\Config;
use Framework\helpers\FileHelper;
use Framework\helpers\StringHelper;
use Framework\routing\Router;
use Modules\DD;

class Application extends Container
{
    public static Application $app;
    public static ?array $config = null;

    // TODO replace this config with index.php
    public string $userClass;

    public Router $router;
    public Request $request;
    public Response $response;
    public Controller $controller;
    public Session $session;
    public Database $db;
    public ?UserModel $user = null;
    public ?View $view = null;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    // TODO maybe rename this method to run - no run is used at the end of a file index.php or command.php?
    public static function create()
    {
        Session::initIfItDoesNotExist();
//        $logger = Logger::getInst();
        $app = new static();
        $config = Config::get();
        static::$config = $config;
//        DD::dd(Config::get());
        $app->userClass = $config['userClass'] ?? '';
        // TODO check and removed if unused
        $app->layout = $config['layout']['value'] ?? '';
        $app->view = new View();
        $app->request = new Request();
        $app->response = new Response();
        $app->session = new Session();
        $app->router = Router::getInstance($app->request, $app->response);
        static::$app = $app;

        $defConnection = $config['default'];
        $dbConfig = $config['connections'][$defConnection];

        $app->db = new Database($dbConfig);

        $primaryValue = $app->session->get('user');
        if ($primaryValue) {
            $primaryKey = $app->userClass::primaryKey();
            $app->user = $app->userClass::findOne([$primaryKey => $primaryValue]);
        }

//        $basePath = $app->resolve('paths.base');
        $basePath = basePath();
//        DD::dd(1);

        $app->bindProviders($basePath);
//        $app->configure($basePath);
//        $app->dispatch($basePath);
    }

    public static function app(): Application
    {
        try {
            if (static::$app) {
                return static::$app;
            }
            throw new \Exception("Application is not yet defined!");
        } catch (\Exception $exception) {
            exit($exception->getMessage());
        }
    }

    public static function getInstance()
    {
        return self::app();
    }

//    public function getInstance()
//    {
//        if (!static::$app) {
//            static::$app = new static();
//        }
//
//        return static::$app;
//    }

    public static function getDb()
    {
        return self::app()->db;
    }

    public function run()
    {
        $page = $this->router->resolve();
        echo $page;
//        try {
//            echo $this->router->resolve();
//        } catch (\Exception $e) {
//            $statusCode = is_int($e->getCode()) ? $e->getCode() : 500;
//            Application::$app->response->setStatusCode($statusCode);
////            dd($e);
//            echo $this->view->renderOnlyView(
//                '_error',
//                [
//                    'exception' => $e
//                ]
//            );
//        }
    }

// TODO check if this can replace run()
//    public function dispatch()
//    {
//        if (is_array($this->handler)) {
//            [$class, $method] = $this->handler;
//            if (is_string($class)) {
//// return (new $class)->{$method}();
//                return app()->call([new $class, $method]);
//            }
//// return $class->{$method}();
//            return app()->call([$class, $method]);
//        }
//// return call_user_func($this->handler);
//        return app()->call($this->handler);
//    }

    // TODO check if can be improved
    public static function getLayout(): string
    {
        return isset(Application::$app->controller) ? Application::$app->controller->layout : Application::$app->layout;
    }

    public function login(UserModel $user): bool
    {
        $this->user = $user;
        $primaryKey = $user::primaryKey();
        $primaryValue = $user->{$primaryKey};
        $this->session->set('user', $primaryValue);

        return true;
    }

    public function auth(): InterfaceAuthBase
    {
        return AuthManager::getInstance();
    }

    public static function isGuest(): bool
    {
        return !static::$app->user;
    }

    public static function isAuth(): bool
    {
        return !empty(static::$app->user);
    }

//    private function configure($basePath)
//    {
//        $dotenv = Dotenv::createImmutable($basePath);
//        $dotenv->load();
//    }

    private function bindProviders($basePath)
    {
        $providerDir = StringHelper::normalizeSlashes(Config::get('providers.directory'));
        $providers = require sprintf("$basePath/%sproviders.php", $providerDir);

        foreach ($providers as $provider) {
            $instance = new $provider();

            if (method_exists($instance, 'bind')) {
                $instance->bind($this);
            }
        }
    }

//    TODO remove if it is unused
//    public function call($callable, array $parameters = [])
//    {
//        $reflector = $this->getReflector($callable);
//
//        $dependencies = [];
//
//        foreach ($reflector->getParameters() as $parameter) {
//            $name = $parameter->getName();
//            $type = $parameter->getType();
//
//            if (isset($parameters[$name])) {
//                $dependencies[$name] = $parameters[$name];
//                continue;
//            }
//
//            if ($parameter->isDefaultValueAvailable()) {
//                $dependencies[$name] = $parameter->getDefaultValue();
//                continue;
//            }
//
//            if ($type instanceof \ReflectionType) {
//                $dependencies[$name] = $this->resolve($type);
//                continue;
//            }
//
//            throw new \InvalidArgumentException("$name cannot be resolved");
//        }
//    }
//
//    /**
//     *
//     * @return \ReflectionMethod|\ReflectionFunction
//     * @throws \ReflectionException
//     */
//    private function getReflector($callable)
//    {
//        if (is_array($callable)) {
//            return new \ReflectionMethod($callable[0], $callable[1]);
//        }
//
//        return new \ReflectionFunction($callable);
//    }
}
