<?php

namespace Framework\helpers;

class Sanitizer
{
    public static function string($str): string
    {
        return (string) filter_var(filter_var(strip_tags(html_entity_decode($str)), FILTER_SANITIZE_STRING), FILTER_SANITIZE_SPECIAL_CHARS);
    }

    public static function integer($num): int
    {
        return (int) filter_var($num, FILTER_SANITIZE_NUMBER_INT);
    }

    public static function int($num): int
    {
        return self::integer($num);
    }

    public static function float($num): float
    {
        return (float) filter_var($num, FILTER_SANITIZE_NUMBER_FLOAT);
    }

}