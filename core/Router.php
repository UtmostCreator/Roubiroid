<?php

namespace app\core;

use modules\DD\DD;

class Router
{
    public Request $request;
    public Response $response;
    protected array $routes = [];
    protected string $viewFolder = '/views';

    /**
     * Router constructor.
     * @param Request $request
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }


    public function get(string $path, $callback)
    {
        $this->routes['get'][$path] = $callback;
    }

    public function resolve()
    {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();
        $callback = $this->routes[$method][$path] ?? false;

        if ($callback === false) {
            Application::$app->response->setStatusCode(404);
            return 'Not found'; // get Not Found controller
        }

        if (is_string($callback)) {
            return $this->renderView($callback);
        }

        return call_user_func($callback);
//        DD::dd($this->routes);
//        DD::dd($callback);
    }

    private function renderView($view)
    {
        $layoutContent = $this->getLayoutContent('main'); // TODO add a config class with props
        $viewContent = $this->renderOnlyView($view); // TODO add a config class with props

        $replaceContentArr = ['{{content}}', '{{ content }}'];

        return str_replace($replaceContentArr, $viewContent, $layoutContent);
    }

    protected function getLayoutContent($view)
    {
        ob_start();
        require_once Application::$ROOT_DIR . "{$this->viewFolder}/layouts/{$view}.php";
        return ob_get_clean();
    }

    protected function renderOnlyView($view)
    {
        ob_start();
        require_once Application::$ROOT_DIR . "{$this->viewFolder}/{$view}.php";
        return ob_get_clean();
    }

    public function setViewFolder($path) {
        $this->viewFolder =  rtrim($path, '/') . '/'; // TODO normalizeSlashes
    }

    public function resetViewFolder($path) {
        $this->viewFolder = '/views/';
    }

}