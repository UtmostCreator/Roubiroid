<?php

use Framework\db\Connection\Connection;
use Framework\db\Migration\AMigration;

class CreateTestsTable extends AMigration
{
    public static ?string $tableName = 'tests';

    public function up(Connection $connection)
    {
        $table = $connection->createTable(self::$tableName);
        $table->id('id');
        $table->string('name');
        $table->string('description');
        $table->string('answer');
        $table->execute();
    }

    public function down(Connection $connection)
    {
        $connection->dropTable(self::$tableName);
    }}