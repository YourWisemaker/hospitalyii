<?php

use yii\db\Migration;

class m240313_000004_add_status_to_employee extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%employee}}', 'status', $this->string(20)->notNull()->defaultValue('active')->after('address'));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%employee}}', 'status');
    }
}