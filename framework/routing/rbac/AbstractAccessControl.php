<?php

namespace Framework\rbac;

// Policies is used for grouping gates (permissions) or for 1 specific model

// must be registered in AuthServiceProvider in method called booted
abstract class AbstractAccessControl
{
    abstract public function can($user, $accessName);

    abstract public function cannot($user, $accessName);

    abstract public function canany($user, $accessName);

    abstract public static function define($name, $callback);

    abstract public static function allows($name, $task);

//    abstract public static function check($name, $task);
//
//    abstract public static function none($name, $task);
}