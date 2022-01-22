<?php

namespace Framework;

use Modules\DD;

/**
 * Class Response
 *
 * @author Roman Zakhriapa <utmostcreator@gmail.com>
 * @package Framework
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

    public function redirect(string $url, int $statusCode = 303): void
    {
//        DD::dd($statusCode);
        switch ($url) {
            case 'home':
            case '':
            case '/':
                header('Location: ' . URL::getBase(), true, $statusCode);
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
            case 'back':
                $isTheSameUrl = URL::getBase(false) . app()->request->getUri();
                if (!empty(app()->request->getUri()) && $isTheSameUrl !== app()->request->refererPage()) {
                    header('Location: ' . app()->request->refererPage(), true, $statusCode);
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
