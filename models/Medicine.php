<?php

namespace app\models;

use Yii;
use app\models\base\BaseModel;

/**
 * This is the model class for table "medicine".
 *
 * @property int $id
 * @property string $name
 * @property float $price
 * @property int $stock
 * @property string|null $description
 * @property int $created_at
 * @property int $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 *
 * @property MedicineDetail[] $medicineDetails
 */
class Medicine extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%medicine}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'price'], 'required'],
            [['price'], 'number'],
            [['stock', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['description'], 'string'],
            [['name'], 'string', 'max' => 100],
            [['name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Nama Obat',
            'price' => 'Harga',
            'stock' => 'Stok',
            'description' => 'Deskripsi',
            'created_at' => 'Dibuat Pada',
            'updated_at' => 'Diperbarui Pada',
            'created_by' => 'Dibuat Oleh',
            'updated_by' => 'Diperbarui Oleh',
        ];
    }

    /**
     * Gets query for [[MedicineDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMedicineDetails()
    {
        return $this->hasMany(MedicineDetail::class, ['medicine_id' => 'id']);
    }
}
