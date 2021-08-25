<?php

use App\core\Router;

if (!function_exists('app')) {
    /**
     * Get app instance.
     * @return mixed|\App\core\Application
     */
    function app(): \App\core\Application
    {
        return \App\core\Application::app();
    }
}

if (!function_exists('abort')) {
    /**
     * Redirects to error page
     */
    function abort(int $statusCode, string $errMessage = ''): void
    {
//        \modules\DD\DD::dd($errMessage);
        try {
//        \modules\DD\DD::dd(app()->router::getRoutes()['get']);

//            \modules\DD\DD::dd(app()->router::getRoutes());
            $abortMethod = app()->router::getRoutes()[$statusCode];
//        \modules\DD\DD::dd($abortMethod);
            if (isset($abortMethod) && $abortMethod instanceof \Closure) {
//                echo $errMessage;
                $abortMethod();
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        exit;
    }
}

if (!function_exists('contactYourAdministrator')) {
    /**
     * Throw Production Exception
     *
     * @param Throwable $exception
     * @return bool
     */
    function contactYourAdministrator(\Throwable $exception): bool
    {
        echo "Please, contact your administrator and provide the steps to reproduce this error!";
//        echo 'Exception: ' . $exception->getMessage();
        return true;
    }
}

if (!function_exists('auth')) {
    /**
     * Get app instance.
     * @return \App\core\authentication\AuthManager
     */
    function auth(): \App\core\authentication\InterfaceAuthBase
    {
        return \App\core\Application::app()->auth(); // AuthManager
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
        return \App\core\Application::isAuth();
    }
}

if (!function_exists('isGuest')) {
    /**
     * Get app instance.
     * @return bool
     */
    function isGuest(): bool
    {
        return \App\core\Application::isGuest();
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