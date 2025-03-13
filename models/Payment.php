<?php

namespace app\models;

use Yii;
use app\models\base\BaseModel;

/**
 * This is the model class for table "payment".
 *
 * @property int $id
 * @property int $medical_record_id
 * @property float $amount
 * @property string $payment_date
 * @property string $payment_method
 * @property string $status
 * @property int $created_at
 * @property int $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 *
 * @property MedicalRecord $medicalRecord
 */
class Payment extends BaseModel
{
    const STATUS_PENDING = 'pending';
    const STATUS_PARTIAL = 'partial';
    const STATUS_PAID = 'paid';
    
    const METHOD_CASH = 'cash';
    const METHOD_DEBIT = 'debit';
    const METHOD_CREDIT = 'credit';
    const METHOD_INSURANCE = 'insurance';
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%payment}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['medical_record_id', 'amount', 'payment_date', 'payment_method'], 'required'],
            [['medical_record_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['amount'], 'number'],
            [['payment_date'], 'safe'],
            [['status'], 'string', 'max' => 50],
            [['status'], 'default', 'value' => self::STATUS_PENDING],
            [['status'], 'in', 'range' => [self::STATUS_PENDING, self::STATUS_PARTIAL, self::STATUS_PAID]],
            [['payment_method'], 'string', 'max' => 50],
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
            'amount' => 'Jumlah Bayar',
            'payment_date' => 'Tanggal Pembayaran',
            'payment_method' => 'Metode Pembayaran',
            'status' => 'Status',
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
     * Get status label
     */
    public function getStatusLabel()
    {
        $labels = [
            self::STATUS_PENDING => 'Belum Lunas',
            self::STATUS_PARTIAL => 'Lunas Sebagian',
            self::STATUS_PAID => 'Lunas',
        ];
        
        return isset($labels[$this->status]) ? $labels[$this->status] : $this->status;
    }
    
    /**
     * Get payment method options
     */
    public static function getPaymentMethodOptions()
    {
        return [
            self::METHOD_CASH => 'Tunai',
            self::METHOD_DEBIT => 'Kartu Debit',
            self::METHOD_CREDIT => 'Kartu Kredit',
            self::METHOD_INSURANCE => 'Asuransi',
        ];
    }
    
    /**
     * Get payment method label
     */
    public function getPaymentMethodLabel()
    {
        $options = self::getPaymentMethodOptions();
        return isset($options[$this->payment_method]) ? $options[$this->payment_method] : $this->payment_method;
    }
    
    /**
     * Check if payment amount matches medical record total
     */
    public function isPaymentComplete()
    {
        $medicalRecord = $this->medicalRecord;
        if ($medicalRecord) {
            return $this->amount >= $medicalRecord->getTotalAmount();
        }
        return false;
    }
    
    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            // If inserting a new record and payment is complete, mark as paid
            if ($insert && $this->isPaymentComplete()) {
                $this->status = self::STATUS_PAID;
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
        
        // If payment is completed, update medical record status
        if ($this->status === self::STATUS_PAID) {
            $medicalRecord = $this->medicalRecord;
            if ($medicalRecord && $medicalRecord->status !== MedicalRecord::STATUS_COMPLETED) {
                $medicalRecord->status = MedicalRecord::STATUS_COMPLETED;
                $medicalRecord->save(false);
            }
        }
    }
}
