<?php

if (!function_exists('app')) {
    /**
     * Get app instance.
     * @return mixed|\app\core\Application
     */
    function app(): \app\core\Application
    {
        return \app\core\Application::app();
    }
}

if (!function_exists('auth')) {
    /**
     * Get app instance.
     * @return \app\core\authentication\AuthManager
     */
    function auth(): \app\core\authentication\InterfaceAuthBase
    {
        return \app\core\Application::app()->auth(); // AuthManager
    }
}

if (!function_exists('dd')) {
    /**
     * Get app instance.
     * @param mixed $args
     */
    function dd($args)
    {
        return \modules\DD\DD::dd($args); // AuthManager
    }
}

if (!function_exists('isAuth')) {
    /**
     * Get app instance.
     * @return bool
     */
    function isAuth(): bool
    {
        return \app\core\Application::isAuth();
    }
}

if (!function_exists('isGuest')) {
    /**
     * Get app instance.
     * @return bool
     */
    function isGuest(): bool
    {
        return \app\core\Application::isGuest();
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