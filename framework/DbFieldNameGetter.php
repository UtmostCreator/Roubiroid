<?php

namespace Framework;

class DbFieldNameGetter extends BaseObject
{
    public function __get($name)
    {
        try {
            $value = parent::__get($name);
            if (!is_null($value)) {
                return $value;
            }

            $propName = $name;
            $arr = explode('_', $name);
            $arr = array_map(fn($el) => ucfirst($el), $arr);
            $name = implode('', $arr);
            $methodName = "get{$name}Attribute";
            //        dd($methodName);
            if (method_exists($this, $methodName)) {
                return $this->{$methodName}();
            }
            throw new \InvalidArgumentException("There is no such field name like \"{$propName}\"");
        } catch (\Exception $exception) {
            exit($exception->getMessage());
        }
    }
}