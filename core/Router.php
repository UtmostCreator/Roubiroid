<?php

namespace app\core;

use modules\DD\DD;

class Router
{
    public Request $request;
    public Response $response;
    protected array $routes = [];
    protected string $viewFolder = '/views';
    protected string $layoutsFolder = 'layouts';
    protected string $layoutName = 'main';

    protected string $ext = '.php';

    /**
     * Router constructor.
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    protected function getLayoutContent()
    {
        $layout = Application::$app->controller->layout;
        ob_start();
        require_once Application::$ROOT_DIR . "{$this->viewFolder}/{$this->layoutsFolder}/{$layout}.php";
        return ob_get_clean();
    }

    public function get(string $path, $callback)
    {
        $this->routes['get'][$path] = $callback;
    }

    public function post(string $path, $callback)
    {
        $this->routes['post'][$path] = $callback;
    }

    public function resolve()
    {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();
        $callback = $this->routes[$method][$path] ?? false;

        if ($callback === false) {
            Application::$app->response->setStatusCode(404);
            return $this->renderView('_404'); // get Not Found controller
        }

        // this is applied to static functions
        if (is_string($callback)) {
            return $this->renderView($callback);
        }

        // creates an object to call a non-static method
        if (is_array($callback)) {
            Application::$app->controller = new $callback[0]();
            $callback[0] = Application::$app->controller;
        }

        // this is applied to non-static functions
        return call_user_func($callback, $this->request); // params to the method of a controller $this->request
    }

    public function renderView($view, array $params = [])
    {
//        DD::dd(Application::$ROOT_DIR . $this->viewFolder . '/'. $this->layoutsFolder . '/' . $this->layoutName . $this->ext);
        if (!file_exists(Application::$ROOT_DIR . $this->viewFolder . '/' . $this->layoutsFolder . '/' . $this->layoutName . $this->ext)) {
            echo "View file '{$this->layoutName}' was not found";
            exit;
        }
        if (!file_exists(Application::$ROOT_DIR . $this->viewFolder . '/' . $view . $this->ext)) {
            echo "View file '{$view}' was not found";
            exit;
        }
        $layoutContent = $this->getLayoutContent(); // $this->layoutName
        $viewContent = $this->renderOnlyView($view, $params); // TODO add a config class with props
        $replaceContentArr = ['{{content}}', '{{ content }}'];

        return str_replace($replaceContentArr, $viewContent, $layoutContent);
    }

    public function renderContent($content)
    {
        $layoutContent = $this->getLayoutContent('main'); // TODO add a config class with props
        $replaceContentArr = ['{{content}}', '{{ content }}'];

        return str_replace($replaceContentArr, $content, $layoutContent);
    }

    public function renderOnlyView($view, array $params = [])
    {
        extract($params);
//        foreach ($params as $key => $value) {
//            $$key = $value;
//        }

//        DD::dd($name);
//        DD::dd($arr);
        ob_start();
        require_once Application::$ROOT_DIR . "{$this->viewFolder}/{$view}.php";
        return ob_get_clean();
    }

    public function setViewFolder($path)
    {
        $this->viewFolder = rtrim($path, '/') . '/'; // TODO normalizeSlashes
    }

    public function resetViewFolder($path)
    {
        $this->viewFolder = '/views';
    }

    public function setLayout($layoutName)
    {
        $this->doesLayoutExist($layoutName);
        $this->layoutName = $layoutName;
    }

    /**
     * @param $layoutName
     */
    protected function doesLayoutExist($layoutName): void
    {
        if (!file_exists(Application::$ROOT_DIR . $this->viewFolder . '/' . $this->layoutsFolder . '/' . $layoutName . $this->ext)) {
            echo "Layout file '$layoutName' was not found!";
            exit;
        }
    }

}
