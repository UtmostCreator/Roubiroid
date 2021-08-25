<?php

namespace App\core;

interface IAccessControl
{
//    public static function can($user, $task);

    public static function create($user);

    public static function update($user, $task);

    public static function delete($user, $task);

}