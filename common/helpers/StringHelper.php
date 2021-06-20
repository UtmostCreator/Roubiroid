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
}
