<?php

namespace App\core;

use modules\DD\DD;

/**
 * Class Response
 *
 * @author Roman Zakhriapa <utmostcreator@gmail.com>
 * @package App\core
 */
class Response
{
    public const SERVER_INTERNAL_ERROR = 500;
    public const CLIENT_NOT_FOUND = 404;
    public const CLIENT_BAD_REQUEST = 400;
    public const REDIRECT_OTHER_REDIRECT = 303;
    public const REDIRECT_MOVED_TEMPORARILY = 302;
    public const REDIRECT_MOVED_PERMANENTLY = 301;

    public function setStatusCode(int $code)
    {
        http_response_code($code);
    }

    public static function redirectForeverTo($path = '/')
    {
        header('Location: ' . URL::getBase(false) . $path, $replace = true, $response_code = 301);
        exit;
    }

    public function redirect(string $url, $statusCode = 303): void
    {
//        DD::dd($statusCode);
        switch ($url) {
            case 'home':
            case '/':
                header('Location: ' . URL::getBase(), true, $statusCode);
                exit();
                break;
            case 'error':
                throw new \InvalidArgumentException('Error; Incorrect URL is specified');
//                $custErrContr = new CustomErrorController(false);
//                $custErrContr->actionContactYourAdministrator('Please Contact Your Administrator or your Developer for more information');
//                header('Location: ' . URL::getBase() . 'forbidden.php', true, 500);
                break;
            case 'forbidden':
                $statusCode = 403;
                header('Location: ' . URL::getBase() . 'forbidden.php', true, $statusCode);
                break;
            case '':
            case 'back':
                $isTheSameUrl = URL::getBase(false) . $_SERVER['REQUEST_URI'];
                if (isset($_SERVER['HTTP_REFERER']) && $isTheSameUrl !== $_SERVER['HTTP_REFERER']) {
                    header('Location: ' . $_SERVER['HTTP_REFERER'], true, $statusCode);
                } else {
                    header('Location: ' . URL::getBase(false), true, $statusCode);
                }
                header("Refresh:0");
                break;
            default:
                header('Location: ' . URL::getBase(false) . $url, true, $statusCode);
                break;
        }

        exit;
    }
}
