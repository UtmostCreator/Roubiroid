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

    /**
     * @param string $haystack
     * @param array $needle
     * @param int $offset
     * @return bool|int
     */
    public static function arrGetEncounteredItemPos($haystack, $needle, $offset = 0): int
    {
        if (!is_array($needle)) {
            $needle = array($needle);
        }
        foreach ($needle as $key => $query) {
            if ($pos = strpos($haystack, $query, $offset) !== false) {
                return $key; // stops at the first true result
            }
        }
        return -1;
    }

    /**
     * @param string $haystack
     * @param array $needle
     * @param int $offset
     * @return bool
     */
    public static function strposa($haystack, $needle, $offset = 0): bool
    {
        if (!is_array($needle)) {
            $needle = array($needle);
        }
        foreach ($needle as $query) {
            if (strpos($haystack, $query, $offset) !== false) {
                return true; // stop on first true result
            }
        }
        return false;
    }

    public function removeValue(array $arr, $value): array
    {
        if (($key = array_search($value, $arr)) !== false) {
            unset($arr[$key]);
        }
        return $arr;
    }
}
