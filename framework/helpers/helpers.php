<?php

use Framework\Application;
use Framework\authentication\AuthManager;
use Framework\authentication\InterfaceAuthBase;
use Framework\View\Engine\BaseEngine;
use Framework\View\Engine\PhpEngine;
use Framework\View\Manager;

if (!function_exists('app')) {
    /**
     * Get app instance.
     * @return mixed|Application
     */
    function app(): Application
    {
        return Application::app();
    }
}

if (!function_exists('abort')) {
    /**
     * Redirects to error page
     */
    function abort(int $statusCode, string $errMessage = ''): void
    {
//        \Modules\DD\DD::dd($errMessage);
        try {
//        \Modules\DD\DD::dd(app()->router::getRoutes()['get']);

//            \Modules\DD\DD::dd(app()->router::getRoutes());
            $abortMethod = app()->router::getRoutes()[$statusCode];
//        \Modules\DD\DD::dd($abortMethod);
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
     * @return AuthManager
     */
    function auth(): InterfaceAuthBase
    {
        return Application::app()->auth(); // AuthManager
    }
}

if (!function_exists('dd')) {
    /**
     * Get app instance.
     * @param mixed $args
     */
    function dd($args)
    {
        \Modules\DD::dd($args); // AuthManager
    }
}

if (!function_exists('isAuth')) {
    /**
     * Get app instance.
     * @return bool
     */
    function isAuth(): bool
    {
        return Application::isAuth();
    }
}

if (!function_exists('isGuest')) {
    /**
     * Get app instance.
     * @return bool
     */
    function isGuest(): bool
    {
        return Application::isGuest();
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

if (!function_exists('view')) {
    function view(string $template, array $data): string
    {
        static $manager;

        if (!$manager) {
            $manager = new Manager();

            // let's add a pth for our views folder
            // so the manager knows where to look for view
            $manager->addPath(base_path() . '/resources/views');
            // we'll also start adding new engine classes
            // with their expected extensions to be able to pick
            // the appropriate engine for the template
            $manager->addEngine('basic.php', new BaseEngine());

            // must be registered last, because the first extension match is returned
            $manager->addEngine('php', new PhpEngine());
        }

        return $manager->render($template, $data);
    }
}