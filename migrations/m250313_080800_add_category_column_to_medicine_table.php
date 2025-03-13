<?php

use yii\db\Migration;

/**
 * Class m250313_080800_add_category_column_to_medicine_table
 */
class m250313_080800_add_category_column_to_medicine_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%medicine}}', 'category', $this->integer()->defaultValue(1)->after('name'));
        
        // Set default category for existing medicines
        $this->update('{{%medicine}}', ['category' => 1]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%medicine}}', 'category');
    }
}
