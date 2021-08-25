<?php

namespace App\core\db\migration;

interface IMigration
{
    public function up();

    public function down();

//    public function safeUp();
//
//    public function safeDown();
}