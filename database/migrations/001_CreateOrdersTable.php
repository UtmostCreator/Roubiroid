<?php

use Framework\db\Connection\Connection;
use Framework\db\Migration\Field\DateTimeField;

class CreateOrdersTable
{
    public static string $tableName = 'orders';

    public function migrate(Connection $connection)
    {
        $table = $connection->createTable(self::$tableName);
        $table->id('id');
        $table->string('name');
        $table->string('description');
        $table->execute();
    }
}