<?php

namespace Framework\authentication;

use Framework\UserModel;

interface InterfaceAuthBase
{
    public static function getInstance();

    public function user(): UserModel;

    public function check(): bool;
}
