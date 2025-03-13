<?php

use yii\db\Migration;

/**
 * Class m250313_080500_add_code_column_to_medicine_table
 */
class m250313_080500_add_code_column_to_medicine_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%medicine}}', 'code', $this->string(20)->after('id'));
        $this->createIndex('idx-medicine-code', '{{%medicine}}', 'code', true);
        
        // Generate default codes for existing medicines
        $medicines = (new \yii\db\Query())
            ->select(['id', 'name'])
            ->from('{{%medicine}}')
            ->all();
            
        foreach ($medicines as $medicine) {
            $code = 'MED' . str_pad($medicine['id'], 3, '0', STR_PAD_LEFT);
            $this->update('{{%medicine}}', ['code' => $code], ['id' => $medicine['id']]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx-medicine-code', '{{%medicine}}');
        $this->dropColumn('{{%medicine}}', 'code');
    }
}
