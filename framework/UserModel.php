<?php

namespace Framework;

use Framework\db\DbModel;

abstract class UserModel extends DbModel
{
    public bool $is_admin = false;

    abstract public function getDisplayName();

    abstract public function getUserRole();

}