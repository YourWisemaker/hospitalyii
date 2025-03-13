<?php

use yii\db\Migration;

class m240313_000001_add_doctor_id_to_medical_record extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%medical_record}}', 'doctor_id', $this->integer()->null()->after('patient_id'));
        $this->addForeignKey(
            'fk-medical_record-doctor_id',
            '{{%medical_record}}',
            'doctor_id',
            '{{%employee}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-medical_record-doctor_id', '{{%medical_record}}');
        $this->dropColumn('{{%medical_record}}', 'doctor_id');
    }
}