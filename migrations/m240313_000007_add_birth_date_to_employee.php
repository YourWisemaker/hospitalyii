<?php

use yii\db\Migration;

class m240313_000007_add_birth_date_to_employee extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%employee}}', 'birth_date', $this->date()->null()->after('name'));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%employee}}', 'birth_date');
    }
}