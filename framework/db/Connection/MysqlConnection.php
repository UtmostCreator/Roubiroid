<?php

namespace Framework\db\Connection;

use Framework\db\Exception\ConnectionException;
use Framework\db\Migration\MysqlMigration;
use Framework\db\QueryBuilder\MysqlQueryBuilder;
use Modules\DD;
use PDO;

class MysqlConnection extends Connection
{
    private PDO $pdo;
    private string $database;

    /**
     * @param $config
     */
    public function __construct($config)
    {
        [
            'host' => $host,
            'port' => $port,
            'database' => $database,
            'username' => $username,
            'password' => $password,
        ] = $config;

        if (empty($host) || empty($database) || empty($username)) {
            throw new \InvalidArgumentException('Connection incorrectly configured');
        }

        $this->database = $database;

        $dsn = sprintf("mysql:host=%s;port=%s;dbname=%s;charset=utf8", $host, $port, $database);
//        DD::dd($dsn);
        $this->createPdo($dsn, $username, $password);
    }


    public function createPdo(string $dsn, $username, $password)
    {
        try {
            $this->pdo = new PDO($dsn, $username, $password);
            if (isDev()) {
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                /* Internally, if you use PDO::ATTR_EMULATE_PREPARES, PDO makes a copy of the SQL query
                and interpolates parameter values into it before doing the prepare and execute.
                But PDO does not expose this modified SQL query. */
                $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            }
        } catch (\PDOException $e) {
            if (isDev()) {
                print "Error!: " . $e->getMessage() . "<br/>";
                die();
            } else {
                throw new ConnectionException('Connection incorrectly configured');
            }
        }
    }

    public function pdo(): PDO
    {
        return $this->pdo;
    }

    public function query(): MysqlQueryBuilder
    {
        return new MysqlQueryBuilder($this);
    }

    public function createTable(string $table): MysqlMigration
    {
        return new MysqlMigration($this, $table, 'create');
    }

    public function alterTable(string $table): MysqlMigration
    {
        return new MysqlMigration($this, $table, 'alter');
    }

    public function getTables(): array
    {
        $statement = $this->pdo()->prepare('SHOW TABLES');
        $statement->execute();

        $results = $statement->fetchAll(PDO::FETCH_NUM);
        $results = array_map(fn($result) => $result[0], $results);

        return $results;
    }

    public function hasTable(string $name): bool
    {
        $tables = $this->getTables();
        return in_array($name, $tables);
    }

    public function dropTables(): array
    {
        // generate an array of "DROP TABLE IF EXISTS `users`" rows
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $statement = $this->pdo->prepare("
            SELECT CONCAT('DROP TABLE IF EXISTS `', table_name, '`')
            FROM information_schema.tables
            WHERE table_schema = '{$this->database}';
        ");

        $statement->execute();

        $dropTableClauses = $statement->fetchAll(PDO::FETCH_NUM);
        // TODO migration
//        DD::dd($dropTableClauses);
        $dropTableClauses = array_map(fn($result) => $result[0], $dropTableClauses);
        // TODO get table names from migration table and only remove them.
        $clauses = [
//            'SET FOREIGN_KEY_CHECKS = 0',
            ...$dropTableClauses,
//            'SET FOREIGN_KEY_CHECKS = 1',
        ];
//        DD::dd(join(';', $clauses) . ';');

//        var_dump(join(';', $clauses) . ';');
//        exit;
        // OLD way
//        $statement = $this->pdo->prepare(join(';', $clauses) . ';');
//        return $statement->execute();
        $totalTableCount = count($clauses);
        $droppedTableCount = 0;
        $droppedList = [];
        foreach ($clauses as $key => $tableToDrop) {
            $statement = $this->pdo->prepare($tableToDrop);
            if ($statement->execute()) {
                preg_match('/`([^"]+)`/', $tableToDrop, $m);
                $droppedList[] = sprintf("\t%s dropped successfully", $m[0]) . PHP_EOL;
//                exit;
                $droppedTableCount++;
            }
        }

        return [
            'allDropped' => $totalTableCount === $droppedTableCount,
            'toBeDroppedCount' => $totalTableCount,
            'droppedTablesCount' => $droppedTableCount,
            'completePercentage' => ($totalTableCount / $droppedTableCount) * 100,
            'droppedList' => $droppedList,
        ];
    }

    public function dropTable(string $name): bool
    {
        $statement = $this->pdo->prepare("DROP TABLE IF EXISTS `{$name}`");
        return $statement->execute();
    }

    public function hasTableOnly(string $name): bool
    {
        $statement = $this->pdo->prepare("SELECT exists(SELECT * 
            FROM information_schema.tables
            WHERE table_schema = '{$this->database}' 
                AND table_name = '{$name}'
            LIMIT 1) as output
        ");
        return $statement->execute();
    }

    public function isTableEmpty(string $name): bool
    {
        $statement = $this->pdo->prepare("SELECT (exists(select 1 from {$name})) as output");
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $result['output'] === 0;
    }

    public function truncate(string $name)
    {
        // TRUNCATE TABLE `tableName`
    }

//    public function truncateTable(string $name): bool
//    {
//        $statement = $this->pdo->prepare("TRUNCATE `rz_framework`.`migrations`");
//        return $statement->execute();
//    }
}
