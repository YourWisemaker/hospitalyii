<?php

use yii\db\Migration;

class m250313_065255_add_columns_to_treatment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250313_065255_add_columns_to_treatment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250313_065255_add_columns_to_treatment cannot be reverted.\n";

        return false;
    }
    */
}
