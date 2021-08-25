<?php


namespace App\core\db\migration;


use App\core\helpers\FileHelper;
use App\core\Application;

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