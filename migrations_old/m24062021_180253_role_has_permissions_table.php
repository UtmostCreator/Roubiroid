<?php

use Framework\db\Migration\IMigration;

class m24062021_180253_role_has_permissions_table implements IMigration
{

    public function up()
    {
        $db = \Framework\Application::$app->db;
        $sql = "CREATE TABLE IF NOT EXISTS `role_has_permissions` (
                  `permission_id` bigint(20) UNSIGNED NOT NULL,
                  `role_id` bigint(20) UNSIGNED NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $sql .= "ALTER TABLE `role_has_permissions`
                      ADD PRIMARY KEY (`permission_id`,`role_id`),
                      ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);
                  ALTER TABLE `role_has_permissions`
                      ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
                      ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;";
        return $db->pdo->exec($sql) !== false;
    }

    public function down()
    {
        // TODO: Implement down() method.
    }
}