<?php


namespace Framework\helpers;


/**
 * Class FileHelper
 *
 * @author Roman Zakhriapa <utmostcreator@gmail.com>
 * @package Framework\helpers
 */
class FileHelper
{
    public static function createDir($destination, $permissions = 0755, $recursive = true)
    {
        return mkdir($destination, $permissions, $recursive);
    }

    public static function canADirectoryBeCreated(string $path): bool
    {
        // is_writable($path)
        if (self::isDirectoryExist($path) || is_file($path) || strlen($path) <= 0) {
            return false;
        }
        return true;
    }

    public static function isDirectoryExist(string $pathToUserDir): bool
    {
        return is_dir($pathToUserDir) && file_exists($pathToUserDir);
    }


    public static function getAllFilesAndFolderIn(string $path, $skipCurrAndParentDir = true): array
    {
        $files = scandir($path);
        if ($skipCurrAndParentDir && $files) {
            return array_diff($files, ['.', '..']);
        }
        return $files;
    }

    public static function mergePath(array $paths): string
    {
        return implode(DIRECTORY_SEPARATOR, $paths);
    }

}