<?php

namespace app\models;

use Yii;
use app\models\base\BaseModel;

/**
 * This is the model class for table "medicine_detail".
 *
 * @property int $id
 * @property int $medical_record_id
 * @property int $medicine_id
 * @property int $quantity
 * @property float $price
 * @property int $created_at
 * @property int $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 *
 * @property Medicine $medicine
 * @property MedicalRecord $medicalRecord
 */
class MedicineDetail extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%medicine_detail}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['medical_record_id', 'medicine_id', 'quantity', 'price'], 'required'],
            [['medical_record_id', 'medicine_id', 'quantity', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['price'], 'number'],
            [['medicine_id'], 'exist', 'skipOnError' => true, 'targetClass' => Medicine::class, 'targetAttribute' => ['medicine_id' => 'id']],
            [['medical_record_id'], 'exist', 'skipOnError' => true, 'targetClass' => MedicalRecord::class, 'targetAttribute' => ['medical_record_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'medical_record_id' => 'Rekam Medis',
            'medicine_id' => 'Obat',
            'quantity' => 'Jumlah',
            'price' => 'Harga',
            'created_at' => 'Dibuat Pada',
            'updated_at' => 'Diperbarui Pada',
            'created_by' => 'Dibuat Oleh',
            'updated_by' => 'Diperbarui Oleh',
        ];
    }

    /**
     * Gets query for [[Medicine]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMedicine()
    {
        return $this->hasOne(Medicine::class, ['id' => 'medicine_id']);
    }

    /**
     * Gets query for [[MedicalRecord]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMedicalRecord()
    {
        return $this->hasOne(MedicalRecord::class, ['id' => 'medical_record_id']);
    }
    
    /**
     * Calculate subtotal for this medicine detail
     */
    public function getSubtotal()
    {
        return $this->price * $this->quantity;
    }
    
    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            // If price is not set, get it from the medicine
            if ($insert && empty($this->price)) {
                $medicine = Medicine::findOne($this->medicine_id);
                if ($medicine) {
                    $this->price = $medicine->price;
                }
            }
            
            return true;
        }
        return false;
    }
    
    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        
        // Update medicine stock
        $medicine = $this->medicine;
        if ($medicine) {
            // Deduct stock when adding medicine to medical record
            if ($insert) {
                $medicine->stock = $medicine->stock - $this->quantity;
                $medicine->save();
            }
            // Handle stock updates when modifying quantity
            elseif (isset($changedAttributes['quantity'])) {
                $oldQuantity = $changedAttributes['quantity'];
                $quantityDiff = $this->quantity - $oldQuantity;
                
                if ($quantityDiff != 0) {
                    $medicine->stock = $medicine->stock - $quantityDiff;
                    $medicine->save();
                }
            }
        }
    }
    
    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
        parent::afterDelete();
        
        // Return stock when deleting medicine from medical record
        $medicine = $this->medicine;
        if ($medicine) {
            $medicine->stock = $medicine->stock + $this->quantity;
            $medicine->save();
        }
    }
}
