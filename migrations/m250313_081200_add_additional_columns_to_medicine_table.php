<?php

use yii\db\Migration;

/**
 * Class m250313_081200_add_additional_columns_to_medicine_table
 */
class m250313_081200_add_additional_columns_to_medicine_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Add unit column
        if (!$this->getDb()->getSchema()->getTableSchema('{{%medicine}}')->getColumn('unit')) {
            $this->addColumn('{{%medicine}}', 'unit', $this->string(100)->after('category'));
        }
        
        // Add purchase_price column
        if (!$this->getDb()->getSchema()->getTableSchema('{{%medicine}}')->getColumn('purchase_price')) {
            $this->addColumn('{{%medicine}}', 'purchase_price', $this->decimal(10, 2)->defaultValue(0)->after('unit'));
        }
        
        // Add sell_price column
        if (!$this->getDb()->getSchema()->getTableSchema('{{%medicine}}')->getColumn('sell_price')) {
            $this->addColumn('{{%medicine}}', 'sell_price', $this->decimal(10, 2)->defaultValue(0)->after('price'));
        }
        
        // Add min_stock column
        if (!$this->getDb()->getSchema()->getTableSchema('{{%medicine}}')->getColumn('min_stock')) {
            $this->addColumn('{{%medicine}}', 'min_stock', $this->integer()->defaultValue(0)->after('stock'));
        }
        
        // Set default values for existing medicines
        $this->update('{{%medicine}}', [
            'unit' => 'Pcs',
            'purchase_price' => 0,
            'sell_price' => 0,
            'min_stock' => 0
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%medicine}}', 'min_stock');
        $this->dropColumn('{{%medicine}}', 'sell_price');
        $this->dropColumn('{{%medicine}}', 'purchase_price');
        $this->dropColumn('{{%medicine}}', 'unit');
    }
}
