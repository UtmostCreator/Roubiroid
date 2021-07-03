<?php

namespace app\core;

use app\core\authentication\AuthManager;
use app\core\authentication\InterfaceAuthBase;
use app\core\db\Database;

class Application
{
    public static Application $app;
    public static string $ROOT_DIR;
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

    public function __construct(string $rootPath, array $config)
    {
        static::$config = $config;
        $this->userClass = $config['userClass'] ?? '';
        $this->layout = $config['layout']['value'] ?? '';
        static::$ROOT_DIR = $rootPath;
        $this->view = new View();
        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->router = new Router($this->request, $this->response);
        static::$app = $this;

        $defConnection = $config['default'];
        $dbConfig = $config['connections'][$defConnection];

        $this->db = new Database($dbConfig);

        $primaryValue = $this->session->get('user');
        if ($primaryValue) {
            $primaryKey = $this->userClass::primaryKey();
            $this->user = $this->userClass::findOne([$primaryKey => $primaryValue]);
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

    public function run()
    {
        try {
            echo $this->router->resolve();
        } catch (\Exception $e) {
            $statusCode = is_int($e->getCode()) ? $e->getCode() : 500;
            Application::$app->response->setStatusCode($statusCode);
//            dd($e);
            echo $this->view->renderOnlyView(
                '_error',
                [
                    'exception' => $e
                ]
            );
        }
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
        return realpath(PointTo::getBase() . $path);
    }
}
