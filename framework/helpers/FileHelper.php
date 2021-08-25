<?php


namespace App\core\helpers;


/**
 * Class FileHelper
 *
 * @author Roman Zakhriapa <utmostcreator@gmail.com>
 * @package App\core\helpers
 */
class FileHelper
{

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