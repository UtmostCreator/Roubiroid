<?php

namespace app\core;

class PointTo
{
    public static function getBase(): string
    {
        return '../';
    }

    public static function views($path): string
    {
        return static::getBase() . 'views/' . $path;
    }

    public static function to(string $folderPath, string $file): string
    {
        return $folderPath . $file;
    }

}