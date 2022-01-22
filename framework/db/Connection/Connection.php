<?php

namespace Framework\db\Connection;

use Framework\db\Migration\Migration;
use Framework\db\QueryBuilder\QueryBuilder;
use PDO;

abstract class Connection
{
    /**
     * Get the underlying PDO instance for this connection
     */
    abstract public function pdo(): PDO;

    /**
     * Start a new query on this connection
     */
    abstract public function query(): QueryBuilder;

    /**
     * Start a new migration to add a table on this connection
     */
    abstract public function createTable(string $table): Migration;

    /**
     * Start a new migration to add a table on this connection
     */
    abstract public function alterTable(string $table): Migration;

    /**
     * Return a list of table names on this connection
     */
    abstract public function getTables(): array;

    /**
     * Find out if a table exists on this connection
     */
    abstract public function hasTable(string $name): bool;

    /**
     * Drop all tables in the current database
     * the caveat: iit will only drop tables if the file path is not :memory:.
     * It is possible to have an in-memory SQLite database
     */
    abstract public function dropTables(): int;


    protected static $inst = null;

    public static function setStatic($connection)
    {
        self::$inst = $connection;
    }

    public static function getInst(): self
    {
        if (is_null(self::$inst)) {
            self::$inst = new static();
        }

        return self::$inst;
    }
}