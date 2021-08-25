<?php

use Framework\db\migration\IMigration;

class m24062021_180546_model_has_roles_table implements IMigration
{

    public function up()
    {
        $db = \Framework\Application::$app->db;
        $sql = "CREATE TABLE `model_has_roles` (
                  `role_id` bigint(20) UNSIGNED NOT NULL,
                  `model_type` varchar(255) NOT NULL,
                  `model_id` bigint(20) UNSIGNED NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $sql .= "ALTER TABLE `model_has_roles`
                  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
                  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);
                ALTER TABLE `model_has_roles`
                  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;";

        return $db->pdo->exec($sql) !== false;
    }

    public function down()
    {
        // TODO: Implement down() method.
    }
}