<?php

namespace Framework;

use Modules\DD;

class URL
{
    public static function getBase($leadingSlash = true): string
    {
        // $_SERVER['SERVER_NAME'] == $_SERVER['HTTP_HOST']; but "HTTP_HOST" can be manipulated freely by the user
        $additionalUrl = '';
        if ($_ENV['APP_ENV'] === 'LOCAL') {
            $additionalUrl = '';
        }
        if ($_ENV['APP_ENV'] === 'REMOTE') {
            $additionalUrl = $_ENV['REMOTE_PROJECT_LOC'];
        }
        $additionalUrl .= empty($_SERVER['REQUEST_URI']) && $leadingSlash ? '/' : '';

        return sprintf(
            "%s://%s%s%s",
            isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
            $_SERVER['SERVER_NAME'],
            $additionalUrl,
            $_SERVER['REQUEST_URI']
        );
    }

    public static function isSecure(): bool
    {
        return
            (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || $_SERVER['SERVER_PORT'] == 443;
    }

}