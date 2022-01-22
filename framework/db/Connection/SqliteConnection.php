<?php

namespace Framework\db\Connection;

use Framework\db\QueryBuilder\SqliteQueryBuilder;
use InvalidArgumentException;
use PDO;

class SqliteConnection extends Connection
{
    private PDO $pdo;

    public function __construct(array $config)
    {
        ['path' => $path] = $config;

        if (empty($path)) {
            throw new InvalidArgumentException('Connection incorrectly configured');
        }

        $this->pdo = new PDO("sqlite:{$path}");
    }

    public function pdo(): PDO
    {
        return $this->pdo;
    }

    public function query(): SqliteQueryBuilder
    {
        return new SqliteQueryBuilder($this);
    }

    public function createTable(string $table): SqliteMigration
    {
        return new SqliteMigration($this, $table, 'create');
    }

    public function alterTable(string $table): SqliteMigration
    {
        return new SqliteMigration($this, $table, 'alter');
    }
}