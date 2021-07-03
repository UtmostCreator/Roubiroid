<?php

namespace app\core\authentication;

use app\core\UserModel;

interface InterfaceAuthBase
{
    public static function getInstance();

    public function user(): UserModel;

    public function check(): bool;
}