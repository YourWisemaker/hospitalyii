<?php

use yii\db\Migration;

class m240101_000000_add_columns_to_treatment extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%treatment}}', 'code', $this->string(50)->null());
        $this->addColumn('{{%treatment}}', 'category', $this->integer()->null());
        $this->addColumn('{{%treatment}}', 'status', $this->tinyInteger()->defaultValue(1));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%treatment}}', 'code');
        $this->dropColumn('{{%treatment}}', 'category');
        $this->dropColumn('{{%treatment}}', 'status');
    }
}