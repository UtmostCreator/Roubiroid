<?php


namespace Framework\db\migration;


use Framework\helpers\FileHelper;
use Framework\Application;

class AbstractMigration
{

    public static function getMigrationFilePath($migration): string
    {
        return FileHelper::mergePath([
            Application::$ROOT_DIR,
            Application::$config['migrations']['folder'],
            $migration
        ]);
    }

}