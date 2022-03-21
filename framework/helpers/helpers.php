<?php

use Framework\Application;
use Framework\authentication\AuthManager;
use Framework\authentication\InterfaceAuthBase;
use Framework\Model;
use Framework\Paths;
use Framework\View\View;
use Modules\DD;

/* ==================================== BASE functions ==================================== */
if (!function_exists('app')) {
    function app(string $alias = null)
    {
        if (is_null($alias)) {
            return Application::getInstance();
        }

        return Application::getInstance()->resolve($alias);
    }
}
//defined('basePath') || define('basePath', app()->basePath());
if (!function_exists('basePath')) {
    /**
     * Get the path root path.
     * TODO maybe rename to basePath()
     * @param string|null $newBasePath
     * @param bool $console
     * @return string
     */
    function basePath(string $newBasePath = null, bool $console = false): string
    {
//        if (!$console) {
//            return app('paths.base');
//        }

        return Paths::getBase();
    }
}

if (!function_exists('abort')) {
    /**
     * Redirects to error page
     */
    function abort(int $statusCode, string $errMessage = ''): void
    {
        try {
            $abortMethod = app()->router::getRoutes()[$statusCode];
            if (isset($abortMethod) && $abortMethod instanceof \Closure) {
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
//    function dd(...$args)
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
//        DD::dd($_SERVER['REQUEST_METHOD']);
//        DD::dl('csrf');
        // bin2hex - Convert binary data into hexadecimal representation
        // random_bytes - Generates cryptographically secure pseudo-random bytes
        if (!isset($_SESSION['token'])) {
            $_SESSION['token'] = bin2hex(random_bytes(32));
        }
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
//        DD::dl('secure');
        // hash_equals - Timing attack safe string comparison
        if (!isset($_POST['csrf']) || !isset($_SESSION['token']) || !hash_equals($_SESSION['token'], $_POST['csrf'])) {
            DD::dd('s exit');
            throw new Exception('CSRF token mismatch');
        }
    }
}

if (!function_exists('isDev')) {
    function isDev(): bool
    {
        if (isset($_ENV['APP_ENV']) && in_array(strtolower($_ENV['APP_ENV']), ['dev', 'local'])) {
            return true;
        }

        return false;
    }
}

if (!function_exists('isProd')) {
    function isProd(): bool
    {
        if (isset($_ENV['APP_ENV']) && in_array($_ENV['APP_ENV'], ['prod', 'REMOTE'])) {
            return true;
        }

        return false;
    }
}

/* ==================================== ADDITIONAL ==================================== */

/* ==================================== ADDITIONAL => VIEW HELPERS ==================================== */
if (!function_exists('view')) {
    function view(string $template, array $data = []): string
    {
//        \Modules\DD::dd(app());
        return app('view')->render($template, $data);
    }
}

if (!function_exists('validate')) {
    function validate(Model $model, array $attributes, $validator, array $params = [])
    {
        return app('validator')->validate($model, $attributes, $validator, $params);
    }
}

//if (!function_exists('validate')) {
//    function validate(array $data, array $rules, string $sessionName = 'errors')
//    {
//        return app('validator')->validate($data, $rules, $sessionName);
//    }
//}


if (!function_exists('teste')) {
    function teste(\Framework\Model $model)
    {
        $model->addError('firstname', 'error msg');
    }
}
