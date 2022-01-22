<?php

namespace Framework\db\QueryBuilder;

use Framework\db\Connection\MysqlConnection;

class MysqlQueryBuilder extends QueryBuilder
{
    protected MysqlConnection $connection;

    public function __construct(MysqlConnection $connection)
    {
        $this->connection = $connection;
    }
}
