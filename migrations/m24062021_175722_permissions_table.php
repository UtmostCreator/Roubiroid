<?php

use app\core\db\migration\IMigration;

class m24062021_175722_permissions_table implements IMigration
{

    public function up()
    {
        $db = \app\core\Application::$app->db;
        $sql = "CREATE TABLE IF NOT EXISTS `permissions` (
                  `id` bigint(20) UNSIGNED NOT NULL,
                  `name` varchar(255) NOT NULL,
                  `guard_name` varchar(255) NOT NULL,
                  `created_at` timestamp NULL DEFAULT NULL,
                  `updated_at` timestamp NULL DEFAULT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $sql .= "ALTER TABLE `permissions`
                  ADD PRIMARY KEY (`id`),
                  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);
                ALTER TABLE `permissions`
                  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;";
        return $db->pdo->exec($sql) !== false;
    }

    public function down()
    {
        // TODO: Implement down() method.
    }
}