<?php


namespace app\core;


use modules\DD\DD;

/**
 * Class Response
 *
 * @author Roman Zakhriapa <utmostcreator@gmail.com>
 * @package app\core
 */
class Response
{
    public function setStatusCode(int $code)
    {
        http_response_code($code);
    }

    public function redirect(string $url, $statusCode = 303): void
    {
        switch ($url) {
            case 'home':
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