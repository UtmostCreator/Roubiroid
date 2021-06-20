<?php

namespace app\core\db;

use app\common\helpers\FileHelper;
use app\core\Application;
use app\core\Logger;
use PDO;

/**
 * Class Database
 *
 * @author Roman Zakhriapa <utmostcreator@gmail.com>
 * @package app\core
 */
class Database
{
    public PDO $pdo;

    public function __construct(array $config)
    {
//        Logger::sAdd('New DB Connection Established');
        $dsn = $config['dsn'];
        $user = $config['user'];
        $password = $config['password'];
        try {
            $this->pdo = new PDO($dsn, $user, $password);
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }

        /** throw exceptions */
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if (SERVER_TYPE === 'LOCAL') {
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        }
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    public function applyMigrations()
    {
        $this->createMigrationTable();

        $appliedMigrations = $this->getAppliedMigrations();

        $files = FileHelper::getAllFilesAndFolderIn(FileHelper::mergePath(
            [Application::$ROOT_DIR, Application::$config['migrations']['folder']]
        ));

        $toApplyMigrations = array_diff($files, $appliedMigrations);

        foreach ($toApplyMigrations as $migration) {
            $fileToInclude = FileHelper::mergePath([
                Application::$ROOT_DIR,
                Application::$config['migrations']['folder'],
                $migration
            ]);
            require_once $fileToInclude;

            $className = pathinfo($migration, PATHINFO_FILENAME);
            $instance = new $className();
            Logger::sAdd("Applying migrations: $migration");
            if ($instance->up()) {
                Logger::sAdd("Migrations '$migration' is executed successfully");
            }

            $newMigrations[] = $migration;
        }

        if (!empty($newMigrations)) {
            $this->saveMigrations($newMigrations);
        } else {
            Logger::sAdd("All migrations are up to date!");
        }
    }

    public function createMigrationTable()
    {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE='INNODB';
        ");
    }

    public function getAppliedMigrations(): array
    {
        try {
            $statement = $this->pdo->prepare("SELECT migration FROM migrations");
            $statement->execute();

            // PDO::FETCH_COLUMN - to fetch evey migration column as a single array
            return $statement->fetchAll(PDO::FETCH_COLUMN);
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    protected function saveMigrations($migrations)
    {
        Query::$RESPONSE_MODE = Query::RESPONSE_GET_AFFECTED_ROWS;
        $res = (new Query())->insert('migrations', ['migration'], $migrations);
        return $res;
    }
}
