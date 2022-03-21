<?php

namespace Framework\db\Migration;

use Framework\db\Connection\Connection;

abstract class AMigration
{
    public static ?string $tableName = null;
    public static ?bool $column = false;

    abstract public function up(Connection $connection);

    abstract public function down(Connection $connection);
}