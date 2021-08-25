<?php

use Framework\db\migration\IMigration;

class m04072021_123211_notification_table implements IMigration
{
    // add new property db to create/delete(drop tables)

    public function up()
    {
        $db = \Framework\Application::$app->db;
        $sql = "CREATE TABLE `notifications` (
                  `id` char(36) NOT NULL,
                  `type` varchar(255) NOT NULL,
                  `notifiable_type` varchar(255) NOT NULL,
                  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
                  `data` text NOT NULL,
                  `read_at` timestamp NULL DEFAULT NULL,
                  `created_at` timestamp NULL DEFAULT NULL,
                  `updated_at` timestamp NULL DEFAULT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        // INDEXES
        $sql .= "ALTER TABLE `notifications`
                  ADD PRIMARY KEY (`id`),
                  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);
                COMMIT;";
        return $db->pdo->exec($sql) !== false;
    }

    public function down()
    {
        $tableName = 'notifications';
//        $this->table->drop($tableName);
        // TODO: Implement down() method.
    }
}