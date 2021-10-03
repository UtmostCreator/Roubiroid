<?php

namespace Framework\db\Connection;

use Framework\db\exception\ConnectionException;
use Framework\db\QueryBuilder\MysqlQueryBuilder;
use Modules\DD;
use PDO;

class MysqlConnection extends Connection
{
    private PDO $pdo;

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

        $dsn = sprintf("mysql:host=%s;port=%s;dbname=%s;charset=utf8", $host, $port, $database);
//        DD::dd($dsn);
        $this->pdo = self::createPdo($dsn, $username, $password);
    }


    public static function createPdo(string $dsn, $username, $password): PDO
    {
        try {
            $pdo = new PDO($dsn, $username, $password);
            if (isDev()) {
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            }
        } catch (\PDOException $e) {
            if (isDev()) {
                print "Error!: " . $e->getMessage() . "<br/>";
                die();
            } else {
                throw new ConnectionException('Connection incorrectly configured');
            }
        }
        return $pdo;
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
}
