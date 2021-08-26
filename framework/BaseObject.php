<?php

namespace Framework;

class BaseObject
{
    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->{$name};
        }
        $getMethodName = $this->{('get' . ucfirst($name))};
        if (method_exists($this, $getMethodName)) {
            return $this->{$getMethodName};
        }

        return null;
    }
}