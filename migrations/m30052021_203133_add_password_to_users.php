<?php

use app\core\Application;
use modules\DD\DD;

class m30052021_203133_add_password_to_users
{
    public function up()
    {
        $db = Application::$app->db;
        $sql = "ALTER TABLE users ADD COLUMN password VARCHAR(512) NOT NULL";
        return $db->pdo->exec($sql) !== false;
    }

    public function down()
    {
        echo 'Down migration';
    }
}
