<?php

if (!function_exists('app')) {
    /**
     * Get app instance.
     * @return mixed|\app\core\Application
     */
    function app()
    {
        return \app\core\Application::app();
    }
}

if (!function_exists('base_path')) {
    /**
     * Get the path to the base of the install.
     *
     * @param string $path
     * @return string
     */
    function base_path($path = '')
    {
        return app()->basePath($path);
    }
}