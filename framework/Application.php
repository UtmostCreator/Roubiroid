<?php

namespace Framework;

use Framework\authentication\AuthManager;
use Framework\authentication\InterfaceAuthBase;
use Framework\db\Database;
use Framework\routing\Router;
use Modules\DD;

class Application
{
    public static Application $app;
    public static string $ROOT_DIR;
    public static string $PUBLIC;
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

    // TODO maybe rename this method to run - no run is used at the end of a file index.php or command.php?
    public static function create(string $rootPath, array $config)
    {
        $app = new self();
        static::$config = $config;
        $app->userClass = $config['userClass'] ?? '';
        // TODO check and removed if unused
        $app->layout = $config['layout']['value'] ?? '';
        static::$ROOT_DIR = $rootPath;
        static::$PUBLIC = __DIR__;
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

    public function basePath($path = ''): string
    {
//        return realpath(Paths::getBase() . $path);
        return Application::$ROOT_DIR;
    }

    public function add($param)
    {

    }
}
