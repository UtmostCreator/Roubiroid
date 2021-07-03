<?php

namespace app\common\helpers;

class StringHelper
{

    public static function uppercaseWordsAndReplaceSpecifier($search, $inStr, $replaceWith = ' '): string
    {
        $clearStr = str_replace($search, $replaceWith, $inStr);
        return ucwords($clearStr);
    }

    public static function removeWildCards($value)
    {
        return str_replace(array('|', '%', '_'), array(''), $value);
    }

    public static function escapeWildcards($value)
    {
        return str_replace(array('\\', '%', '_'), array('\\\\', '\\%', '\\_'), $value);
    }

    public static function extractString(string $inValue, string $findVal, bool $before = true)
    {
        if (!$before) {
            return substr($inValue, strpos($inValue, $findVal) + 1);
        }
        return substr($inValue, 0, strpos($inValue, $findVal));
    }
}
