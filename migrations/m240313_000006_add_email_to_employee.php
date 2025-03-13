<?php

use yii\db\Migration;

class m240313_000006_add_email_to_employee extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%employee}}', 'email', $this->string(100)->null()->after('name'));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%employee}}', 'email');
    }
}