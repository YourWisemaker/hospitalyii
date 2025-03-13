<?php

use yii\db\Migration;

class m240313_000005_add_gender_to_employee extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%employee}}', 'gender', $this->string(1)->null()->after('name'));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%employee}}', 'gender');
    }
}