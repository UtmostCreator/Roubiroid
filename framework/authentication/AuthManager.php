<?php

namespace Framework\authentication;

use Framework\Application;
use Framework\UserModel;
use models\User;

class AuthManager extends AbstractAuthBase
{
    public static function getInstance()
    {
        if (static::$auth) {
            return static::$auth;
        }

        static::$auth = new static();
        return static::$auth;
    }

    public function user(): UserModel
    {
        return Application::app()->user ?? new User();
    }

    public function check(): bool
    {
        return Application::isAuth();
    }
}