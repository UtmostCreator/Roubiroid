<?php


namespace app\core;


class Controller
{
    public View $view;
    public string $layout = 'main';

    public function __construct()
    {
        $this->view = new View();
    }

    public function render($view, $params = [])
    {
        return Application::$app->router->renderView($view, $params);
    }

    public function setLayout($layoutName)
    {
        $this->layout = $layoutName;
    }
}
