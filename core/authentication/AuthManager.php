<?php

namespace app\core\authentication;

use app\core\Application;
use app\core\UserModel;
use app\models\User;

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