<?php

use Framework\db\Connection\Connection;
use Framework\db\Migration\AMigration;

class CreateProfilesTable extends AMigration
{
    public static ?string $tableName = 'profiles';

    public function up(Connection $connection)
    {
        $table = $connection->createTable(self::$tableName);
        $table->id('id');
        $table->int('user_id');
        $table->execute();
    }

    public function down(Connection $connection)
    {
        $connection->dropTable(self::$tableName);
    }}
