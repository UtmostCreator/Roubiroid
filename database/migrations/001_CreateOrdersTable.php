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
        $table->int('quantity')->default(1);
        $table->float('price')->nullable();
        $table->bool('is_confirmed')->default(false);
        $table->dateTime('ordered_at')->default(DateTimeField::CURRENT_TIMESTAMP);
        $table->text('notes');
        $table->execute();
    }
}