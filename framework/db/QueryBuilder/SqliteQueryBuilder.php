<?php

namespace Framework\db\QueryBuilder;

use Framework\db\Connection\SqliteConnection;

class SqliteQueryBuilder extends QueryBuilder
{
    protected SqliteConnection $connection;

    /**
     * @param SqliteConnection $connection
     */
    public function __construct(SqliteConnection $connection)
    {
        $this->connection = $connection;
    }
}
