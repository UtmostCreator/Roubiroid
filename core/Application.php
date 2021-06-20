<?php

namespace app\core;

use app\core\db\Database;

class Application
{
    public static Application $app;
    public static string $ROOT_DIR;
    public string $layout = 'main';
    public static $config = null;

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
        $this->userClass = $config['userClass'];
        $this->layout = $config['layout']['value'];
        static::$ROOT_DIR = $rootPath;
        $this->view = new View();
        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->router = new Router($this->request, $this->response);
        static::$app = $this;

        $this->db = new Database($config['db']);

        $primaryValue = $this->session->get('user');
        if ($primaryValue) {
            $primaryKey = $this->userClass::primaryKey();
            $this->user = $this->userClass::findOne([$primaryKey => $primaryValue]);
        }
    }

    public static function getLayout(): string
    {
        return isset(Application::$app->controller) ? Application::$app->controller->layout : Application::$app->layout;
    }

    public static function app(): Application
    {
        if (static::$app) {
            return static::$app;
        }
    }

    public function run()
    {
        try {
            echo $this->router->resolve();
        } catch (\Exception $e) {
            Application::$app->response->setStatusCode($e->getCode());
            echo $this->view->renderView(
                '_error',
                [
                    'exception' => $e
                ]
            );
        }
    }

    public function login(UserModel $user): bool
    {
        $this->user = $user;
        $primaryKey = $user::primaryKey();
        $primaryValue = $user->{$primaryKey};
        $this->session->set('user', $primaryValue);

        return true;
    }

    public function logout()
    {
        $this->session->remove('user');
    }

    public static function isGuest(): bool
    {
        return !static::$app->user;
    }

    public function basePath($path = ''): string
    {
        return realpath(PointTo::getBase() . $path);
    }
}
