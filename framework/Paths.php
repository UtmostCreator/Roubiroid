<?php

namespace Framework;

// TODO use Application class
class Paths
{
    protected static $INDEX_FILE_LOCATION = '';

    public static function setBase($newRoot): void
    {
        self::$INDEX_FILE_LOCATION = $newRoot;
    }

    public static function getBase(): string
    {
        self::$INDEX_FILE_LOCATION = dirname(__FILE__, 2) . DIRECTORY_SEPARATOR;
        return self::$INDEX_FILE_LOCATION;
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