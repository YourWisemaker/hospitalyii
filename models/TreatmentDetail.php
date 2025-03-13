<?php

namespace app\models;

use Yii;
use app\models\base\BaseModel;

/**
 * This is the model class for table "treatment_detail".
 *
 * @property int $id
 * @property int $medical_record_id
 * @property int $treatment_id
 * @property int $quantity
 * @property float $price
 * @property int $created_at
 * @property int $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 *
 * @property MedicalRecord $medicalRecord
 * @property Treatment $treatment
 */
class TreatmentDetail extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%treatment_detail}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['medical_record_id', 'treatment_id', 'price'], 'required'],
            [['medical_record_id', 'treatment_id', 'quantity', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['price'], 'number'],
            [['quantity'], 'default', 'value' => 1],
            [['medical_record_id'], 'exist', 'skipOnError' => true, 'targetClass' => MedicalRecord::class, 'targetAttribute' => ['medical_record_id' => 'id']],
            [['treatment_id'], 'exist', 'skipOnError' => true, 'targetClass' => Treatment::class, 'targetAttribute' => ['treatment_id' => 'id']],
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
            'treatment_id' => 'Tindakan',
            'quantity' => 'Jumlah',
            'price' => 'Biaya',
            'created_at' => 'Dibuat Pada',
            'updated_at' => 'Diperbarui Pada',
            'created_by' => 'Dibuat Oleh',
            'updated_by' => 'Diperbarui Oleh',
        ];
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
     * Gets query for [[Treatment]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTreatment()
    {
        return $this->hasOne(Treatment::class, ['id' => 'treatment_id']);
    }
    
    /**
     * Calculate subtotal for this treatment detail
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
            // If price is not set, get it from the treatment
            if ($insert && empty($this->price)) {
                $treatment = Treatment::findOne($this->treatment_id);
                if ($treatment) {
                    $this->price = $treatment->price;
                }
            }
            
            return true;
        }
        return false;
    }
}
