<?php

use yii\db\Migration;

class m240313_000002_add_complaint_to_medical_record extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%medical_record}}', 'complaint', $this->text()->null()->after('treatment_date'));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%medical_record}}', 'complaint');
    }
}