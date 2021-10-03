<?php

namespace Framework\db\Connection;

use Framework\db\exception\ConnectionException;
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
//    abstract public function createTable(string $table): Migration;

    /**
     * Start a new migration to add a table on this connection
     */
//    abstract public function alterTable(string $table): Migration;
}