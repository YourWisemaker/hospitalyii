<?php

use yii\db\Migration;

class m240313_000003_add_employee_number_to_employee extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%employee}}', 'employee_number', $this->string(20)->unique()->after('id'));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%employee}}', 'employee_number');
    }
}