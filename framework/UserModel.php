<?php

namespace Framework;

//abstract class UserModel extends DbModel
abstract class UserModel extends Model
{
    public bool $is_admin = false;

    abstract public function getDisplayName();

    abstract public function getUserRole();

}