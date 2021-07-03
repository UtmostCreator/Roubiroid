<?php


namespace app\core\db\migration;


use app\common\helpers\FileHelper;
use app\core\Application;

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