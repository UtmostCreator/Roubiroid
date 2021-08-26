<?php


namespace Framework;


class URL
{
    public static function getBase($leadingSlash = true)
    {
        $serverUrl = (!empty($_SERVER['HTTPS']) ? 'https' : 'Http') . '://' . $_SERVER['HTTP_HOST'];
        $serverUrl .= $leadingSlash ? '/' : '';
        if (SERVER_TYPE === 'LOCAL') {
            return $serverUrl;
        }
        if (SERVER_TYPE === 'REMOTE') {
            return $serverUrl . REMOTE_PROJECT_LOC;
        }
        return $_SERVER['HTTPS'];
    }

}