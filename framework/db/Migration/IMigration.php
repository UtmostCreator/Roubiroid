<?php

namespace Framework\db\Migration;

interface IMigration
{
    public function up();

    public function down();

//    public function safeUp();
//
//    public function safeDown();
}