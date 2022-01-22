<?php

namespace Framework\helpers;

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

    /** Depending on the 3rd argument extracts the string value before or after the specified value
     *
     * @param string $inValue
     * @param string $findVal
     * @param bool $before
     * @return false|string
     */
    public static function extractString(string $inValue, string $findVal, bool $before = true)
    {
        if (!$before) {
            return substr($inValue, strpos($inValue, $findVal) + 1);
        }
        return substr($inValue, 0, strpos($inValue, $findVal));
    }

    /** Checks whether the string ends with the specified string value
     *
     * @param $haystack
     * @param $needle
     * @return false|string
     */
    public static function endsWith($haystack, $needle): bool
    {
        $length = strlen($needle);
        if (!$length) {
            return true;
        }
        return substr($haystack, -$length) === $needle;
    }



    public static function normalizeSlashes($path)
    {
        if (strlen($path) < 2) {
            return false;
        }
        return rtrim($path, '/') . '/';
    }}
