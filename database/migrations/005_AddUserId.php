<?php

use Framework\db\Connection\Connection;
use Framework\db\Migration\AMigration;

class AddUserId extends AMigration
{
    public static ?string $tableName = 'orders';
    public static ?bool $column = true;

    public function up(Connection $connection)
    {
        $table = $connection->alterTable(self::$tableName);
        $table->int('user_id');
        $table->execute();
    }

    public function down(Connection $connection)
    {
        if ($connection->hasTable(self::$tableName) && $connection->query()->hasColumn(static::$tableName, 'user_id')) {
            $table = $connection->alterTable(self::$tableName);
            $table->dropColumn('user_id')->execute();
        }
    }
}
