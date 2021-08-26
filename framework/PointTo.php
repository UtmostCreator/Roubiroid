<?php

namespace Framework;

class PointTo
{
    public static function getBase(): string
    {
        return '../';
    }

    public static function views($path): string
    {
        return static::getBase() . 'resources/views/' . $path;
    }

    public static function to(string $folderPath, string $file): string
    {
        return $folderPath . $file;
    }

}