<?php

use Framework\Application;
use Framework\authentication\AuthManager;
use Framework\authentication\InterfaceAuthBase;
use Framework\Paths;
use Framework\View\Engine\AdvancedEngine;
use Framework\View\Engine\BaseEngine;
use Framework\View\Engine\PhpEngine;
use Framework\View\Manager;
use Modules\DD;


/* ==================================== BASE functions ==================================== */
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

//defined('BASE_PATH') || define('BASE_PATH', app()->basePath());
if (!function_exists('base_path')) {
    /**
     * Get the path root path.
     * TODO maybe rename to basePath()
     * @param string $path
     * @param bool $console
     * @return string
     */
    function base_path(string $path = '', bool $console = false): string
    {
        if (!$console) {
            return app()->basePath($path);
        }

        return Paths::getBase();
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

if (!function_exists('redirect')) {
    /**
     * Get app instance.
     * @return AuthManager
     */
    function redirect(string $url, int $statusCode = 303): void
    {
        Application::$app->response->redirect($url);
    }
}

//if (!function_exists('dd')) {
//    /**
//     * Get app instance.
//     * @param mixed $args
//     */
//    function dd($args)
//    {
//        DD::dd($args); // AuthManager
//    }
//}

/* ==================================== AUTHENTICATION - login + password ==================================== */
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
/* ==================================== AUTHENTICATION - login + password ==================================== */
if (!function_exists('csrf')) {
    function csrf(): string
    {
        // bin2hex - Convert binary data into hexadecimal representation
        // random_bytes - Generates cryptographically secure pseudo-random bytes
        $_SESSION['token'] = bin2hex(random_bytes(32));
        return $_SESSION['token'];
    }
}

/* ==================================== AUTHENTICATION => SECURITY ==================================== */
if (!function_exists('secure')) {
    /**
     * @throws Exception
     */
    function secure(): void
    {
        // hash_equals - Timing attack safe string comparison
        if (!isset($_POST['csrf']) || !isset($_SESSION['token']) || !hash_equals($_SESSION['token'], $_POST['csrf'])) {
            throw new Exception('CSRF token mismatch');
        }
    }
}
if (!function_exists('isDev')) {
    function isDev(): bool
    {
        if (isset($_ENV['APP_ENV']) && in_array($_ENV['APP_ENV'], ['dev', 'LOCAL'])) {
            return true;
        }

        return false;
    }
}

/* ==================================== ADDITIONAL ==================================== */

/* ==================================== ADDITIONAL => VIE HELPERS ==================================== */
if (!function_exists('view')) {
    function view(string $template, array $data = []): string
    {
        /* @var Manager $manager */
        static $manager;

        if (!$manager) {
            $manager = new Manager();

            // let's add a pth for our views' folder
            // so the manager knows where to look for a view
            $manager->addPath(base_path() . '/resources/views');
            // we'll also start adding new engine classes
            // with their expected extensions to be able to pick
            // the appropriate engine for the template
            // TODO add priority to engines
            $manager->addEngine('advanced.php', new AdvancedEngine());
            $manager->addEngine('basic.php', new BaseEngine());

            // must be registered last, because the first extension match is returned
            $manager->addEngine('php', new PhpEngine());

//            $manager->addMacro('escape', [MacrosHelper::class, 'escape']);
            $manager->addMacro('escape', fn($value) => htmlspecialchars($value));
            // ... splat operator TODO add comments here
            $manager->addMacro('includes', fn(...$params) => print view(...$params));
        }

//        return $manager->render($template, $data);
        return $manager->resolve($template, $data);
    }
}
