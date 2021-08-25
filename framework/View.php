<?php

namespace App\core;

use modules\DD\DD;

class View
{
    public string $title = '';
    protected string $layoutName = '';
    protected string $ext = 'php';

    /**
     * View constructor.
     */
    public function __construct()
    {
        $this->layoutName = Application::$config['layout']['value'];
    }

    protected function getLayoutContent()
    {
        $this->layoutName = Application::getLayout();
        ob_start();
        require_once $this->getPathToLayoutFile();
        return ob_get_clean();
    }

    public function setLayout($layoutName)
    {
        $this->doesLayoutExist($layoutName);
        $this->layoutName = $layoutName;
    }

    public function renderView($view, array $params = [])
    {
        $this->doesLayoutExist($this->layoutName);
        if (!file_exists($this->getPathToViewFile($view))) {
            throw new \InvalidArgumentException("Specified file {$view} does not exist");
        }
        $viewContent = $this->renderOnlyView($view, $params); // TODO add a config class with props
        $layoutContent = $this->getLayoutContent(); // $this->layoutName
        $replaceContentArr = ['{{content}}', '{{ content }}'];

        return str_replace($replaceContentArr, $viewContent, $layoutContent);
    }

//    public function renderContent($content)
//    {
//        $layoutContent = $this->getLayoutContent(); // TODO add a config class with props
//        $replaceContentArr = ['{{content}}', '{{ content }}'];
//
//        return str_replace($replaceContentArr, $content, $layoutContent);
//    }

    public function renderOnlyView($view, array $params = [])
    {
        extract($params);
        // the same as extract
//        foreach ($params as $key => $value) {
//            $$key = $value;
//        }
        ob_start();
        $pathToView = $this->getPathToViewFile($view);
        require_once $pathToView;
        return ob_get_clean();
    }

    // TODO move all like (set/reset/ get path to layout|file) to Application or to another place for management
    public function setViewFolder($path)
    {
        Application::$config['views']['folder'] = rtrim($path, '/'); // TODO normalizeSlashes
    }

    public function resetViewFolder($path)
    {
        Application::$config['views']['folder'] = 'views';
    }


    /**
     * @param $layoutName
     */
    protected function doesLayoutExist($layoutName): void
    {
//        DD::dd($this->getPathToLayoutFile());
        if (!file_exists($this->getPathToLayoutFile())) {
            throw new \InvalidArgumentException("Layout file '$layoutName' was not found!");
        }
    }

    private function getPathToLayoutFile(): string
    {
        $viewFolder = Application::$config['views']['folder'];
        $layoutsFolder = Application::$config['layout']['folder'];
        return sprintf(
            '%s/%s/%s/%s.%s',
            Application::$ROOT_DIR,
            $viewFolder,
            $layoutsFolder,
            $this->layoutName,
            $this->ext
        );
    }

    private function getPathToViewFile($view): string
    {
        return sprintf(
            "%s/%s/%s.%s",
            Application::$ROOT_DIR,
            Application::$config['views']['folder'],
            $view,
            $this->ext
        );
    }
}
