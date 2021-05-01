<?php


namespace app\core;


class View
{
    /**
     * View constructor.
     */
    public function __construct()
    {

    }

    public function setLayout($layoutName)
    {
        Application::$app->router->setLayout($layoutName);
    }
}
