<?php

use Framework\db\migration\IMigration;

class m24062021_180133_model_has_permissions_table implements IMigration
{

    public function up()
    {
        $db = \Framework\Application::$app->db;
        $sql = "CREATE TABLE IF NOT EXISTS `model_has_permissions` (
                  `permission_id` bigint(20) UNSIGNED NOT NULL,
                  `model_type` varchar(255) NOT NULL,
                  `model_id` bigint(20) UNSIGNED NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $sql .= "ALTER TABLE `model_has_permissions`
                  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
                  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);
                ALTER TABLE `model_has_permissions`
                  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;";
        return $db->pdo->exec($sql) !== false;
    }

    public function down()
    {
        // TODO: Implement down() method.
    }
}