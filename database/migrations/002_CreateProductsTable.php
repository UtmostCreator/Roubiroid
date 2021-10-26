<?php

use Framework\db\Connection\Connection;

class CreateProductsTable
{
    public static string $tableName = 'products';

    public function migrate(Connection $connection)
    {
        $table = $connection->createTable(self::$tableName);
        $table->id('id');
        $table->string('name');
        $table->text('description');
        $table->execute();
    }
}