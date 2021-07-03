<?php

use app\core\db\migration\IMigration;

class m27062021_133255_failed_jobs_table implements IMigration
{

    public function up()
    {
        $db = \app\core\Application::$app->db;
        $sql = "CREATE TABLE IF NOT EXISTS `failed_jobs` (
                  `id` bigint(20) UNSIGNED NOT NULL,
                  `uuid` varchar(255) NOT NULL,
                  `connection` text NOT NULL,
                  `queue` text NOT NULL,
                  `payload` longtext NOT NULL,
                  `exception` longtext NOT NULL,
                  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        // INDEXES
        $sql .= "ALTER TABLE `failed_jobs`
                      ADD PRIMARY KEY (`id`),
                      ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);
                    ALTER TABLE `failed_jobs`
                      MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;";
        return $db->pdo->exec($sql) !== false;
    }

    public function down()
    {
        // TODO: Implement down() method.
    }
}