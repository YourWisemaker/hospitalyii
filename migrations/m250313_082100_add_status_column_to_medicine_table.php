<?php

use yii\db\Migration;

/**
 * Class m250313_082100_add_status_column_to_medicine_table
 */
class m250313_082100_add_status_column_to_medicine_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Add status column if it doesn't exist
        if (!$this->getDb()->getSchema()->getTableSchema('{{%medicine}}')->getColumn('status')) {
            $this->addColumn('{{%medicine}}', 'status', $this->integer()->defaultValue(1)->after('min_stock'));
        }
        
        // Set default status for existing medicines
        $this->update('{{%medicine}}', ['status' => 1]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%medicine}}', 'status');
    }
}
