<?php

use Framework\db\Connection\Connection;
use Framework\db\Migration\AMigration;

class CreateUsersTable extends AMigration
{
    public static ?string $tableName = 'users';

    public function up(Connection $connection)
    {
        $table = $connection->createTable(self::$tableName);
        $table->id('id');
        $table->string('name');
        $table->string('email');
        $table->string('password');
        $table->execute();
    }

    public function down(Connection $connection)
    {
        $connection->dropTable(self::$tableName);
    }}