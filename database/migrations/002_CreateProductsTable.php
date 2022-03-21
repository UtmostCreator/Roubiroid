<?php

use Framework\db\Connection\Connection;
use Framework\db\Migration\AMigration;

class CreateProductsTable extends AMigration
{
    public static ?string $tableName = 'products';

    public function up(Connection $connection)
    {
        $table = $connection->createTable(self::$tableName);
        $table->id('id');
        $table->string('name');
        $table->text('description');
        $table->execute();
    }

    public function down(Connection $connection)
    {
        $connection->dropTable(self::$tableName);
    }}