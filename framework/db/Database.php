<?php

namespace Framework\db;

use Framework\helpers\ArrayHelper;
use Framework\helpers\FileHelper;
use Framework\Application;
use Framework\db\migration\AbstractMigration;
use Framework\Logger;
use PDO;

/**
 * Class Database
 *
 * @author Roman Zakhriapa <utmostcreator@gmail.com>
 * @package Framework
 */
class Database
{
    public PDO $pdo;

    public function __construct(array $config)
    {
//        Logger::sAdd('New DB Connection Established');
//        $dsn = $config['dsn'];
        $dsn = $this->buildDsn($config['host'], $config);
        $user = $config['username'];
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
//        $this->saveMigrations(['1','2']);
//        exit;
        foreach ($toApplyMigrations as $migration) {
            $fileToInclude = AbstractMigration::getMigrationFilePath($migration);
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

    private function buildDsn(string $host, array $dbConfig): string
    {

        return sprintf(
            "%s:host=%s;port=%s;dbname=%s",
            $dbConfig['driver'],
            $host,
            $dbConfig['port'],
            $dbConfig['database']
        );
    }

    public function dropMigrations(): bool
    {
        $appliedMigrations = $this->getAppliedMigrations();
        usort($appliedMigrations, fn($a, $b) => strlen($b) - strlen($a));

        // TODO use each migration down method to drop it
//        foreach ($appliedMigrations as $migration) {
//            $fileToInclude = AbstractMigration::getMigrationFilePath($migration);
//            require_once $fileToInclude;
//
//            $className = pathinfo($migration, PATHINFO_FILENAME);
//            $instance = new $className();
//            Logger::sAdd("Rolling Back Migration: $migration");
//            if ($instance->down()) {
//                Logger::sAdd("Migrations '$migration' is rolled back successfully");
//            }
//
//            $newMigrations[] = $migration;
//        }

        $tableNames = [];
        foreach ($appliedMigrations as $migration) {
            preg_match("/m\d*_\d*_(.*)_table\.php$/", $migration, $matches);
            $tableNames[] = $matches[1] ?? null;
        }
        $tableNames = array_filter($tableNames);

        if ((new Query())->dropTables($tableNames)) {
            Logger::sAdd('All migrations are down!');

            if ((new Query())->truncate('migrations')) {
                Logger::sAdd('Migrations table is truncated!');
                return true;
            }
        }
        return false;
    }
}
