<?php

class m23052021_203133_users_table
{
    public function up()
    {
        $db = \Framework\Application::$app->db;
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id INT(11) UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
            email VARCHAR(255) NOT NULL,
            firstname VARCHAR(50) NOT NULL,
            lastname VARCHAR(50) NOT NULL,
            status TINYINT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=INNODB;";
        $sql .= 'ALTER TABLE `migrations` ADD UNIQUE(`migration`);';
        return $db->pdo->exec($sql) !== false;
    }

    public function down()
    {
        echo 'Down migration';
    }
}
