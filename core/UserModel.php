<?php

namespace app\core;

use app\core\db\DbModel;

abstract class UserModel extends DbModel
{
    public bool $is_admin = false;

    abstract public function getDisplayName();

    abstract public function getUserRole();

}