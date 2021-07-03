<?php

namespace app\common\helpers;

use modules\DD\DD;

function _matcher($m, $str)
{
    if (preg_match('/^hello (\w+)/i', $str, $matches)) {
        $m[] = $matches[1];
    }

    return $m;
}

class ArrayHelper
{

    /* @var Object $obj */
    /* @var array $data */
    /* @return Object $obj */
    public static function fillPropsFromArray(&$obj, array $data): object
    {
        foreach ($data as $key => $value) {
            if (property_exists($obj, $key)) {
                $obj->{$key} = trim($value);
            }
        }

        return $obj;
    }

    public static function isAssoc(array $arr): bool
    {
        if (array() === $arr) {
            return false;
        }

        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    public function removeValue(array $arr, $value): array
    {
        if (($key = array_search($value, $arr)) !== false) {
            unset($arr[$key]);
        }
        return $arr;
    }
}
