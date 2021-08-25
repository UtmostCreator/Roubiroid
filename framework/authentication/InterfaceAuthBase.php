<?php

namespace App\core\authentication;

use App\core\UserModel;

interface InterfaceAuthBase
{
    public static function getInstance();

    public function user(): UserModel;

    public function check(): bool;
}
