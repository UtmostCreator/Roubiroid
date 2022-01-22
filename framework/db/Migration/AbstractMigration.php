<?php


namespace Framework\db\Migration;


use Framework\helpers\FileHelper;
use Framework\Application;

class AbstractMigration
{

    public static function getMigrationFilePath($migration): string
    {
        return FileHelper::mergePath([
            basePath(),
            Application::$config['migrations']['folder'],
            $migration
        ]);
    }

}