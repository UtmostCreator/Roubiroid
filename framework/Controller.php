<?php

namespace Framework;

use Framework\middlewares\BaseMiddleware;
use Framework\routing\Router;

class Controller
{
//    public View $view; // 1st time, then overridden using a better way
    public string $action = '';
    /**
     * @var array of BaseMiddleware objects
     */
    protected array $middlewares = [];
    public string $layout = 'main';
    // TODO check if needed
    public Router $router;

    public function __construct()
    {
        $this->router = Application::app()->router;
//        Session::initIfItDoesNotExist();
//        $this->view = new View(); // 1st time, then overridden using a better way
    }

    public function render($view, $params = [])
    {
        return Application::$app->view->renderView($view, $params);
    }

    // TODO check for removal
    public function setLayout($layoutName): void
    {
        $this->layout = $layoutName;
    }

    protected function registerMiddleware(BaseMiddleware $middleware)
    {
        $this->middlewares[] = $middleware;
    }

    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

}
