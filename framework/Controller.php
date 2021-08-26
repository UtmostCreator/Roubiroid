<?php

namespace Framework;

use Framework\middlewares\BaseMiddleware;

class Controller
{
    public View $view;
    public string $action = '';
    /**
     * @var array of BaseMiddleware objects
     */
    protected array $middlewares = [];
    public string $layout = 'main';

    public function __construct()
    {
        Session::initIfItDoesNotExist();
        $this->view = new View();
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
