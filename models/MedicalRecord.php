<?php

namespace app\models;

use Yii;
use app\models\base\BaseModel;

/**
 * This is the model class for table "medical_record".
 *
 * @property int $id
 * @property int $patient_id
 * @property string $treatment_date
 * @property string|null $diagnosis
 * @property string $status
 * @property string $payment_status
 * @property int $created_at
 * @property int $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 *
 * @property MedicineDetail[] $medicineDetails
 * @property Patient $patient
 * @property Payment[] $payments
 * @property TreatmentDetail[] $treatmentDetails
 */
class MedicalRecord extends BaseModel
{
    const STATUS_ONGOING = 'ongoing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_WAITING_PAYMENT = 'waiting_payment';
    
    // Payment status constants
    const PAYMENT_STATUS_UNPAID = 'unpaid';
    const PAYMENT_STATUS_PARTIAL = 'partial';
    const PAYMENT_STATUS_PAID = 'paid';
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%medical_record}}';
    }

    /**
     * Gets query for [[Doctor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDoctor()
    {
        return $this->hasOne(Employee::class, ['id' => 'doctor_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['patient_id', 'treatment_date'], 'required'],
            [['patient_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['treatment_date'], 'safe'],
            [['diagnosis', 'complaint'], 'string'],  // Add complaint to string validation
            [['status'], 'string'],
            [['status'], 'default', 'value' => self::STATUS_ONGOING],
            [['status'], 'in', 'range' => [self::STATUS_ONGOING, self::STATUS_COMPLETED, self::STATUS_WAITING_PAYMENT]],
            [['payment_status'], 'string'],
            [['payment_status'], 'default', 'value' => self::PAYMENT_STATUS_UNPAID],
            [['payment_status'], 'in', 'range' => [self::PAYMENT_STATUS_UNPAID, self::PAYMENT_STATUS_PARTIAL, self::PAYMENT_STATUS_PAID]],
            [['patient_id'], 'exist', 'skipOnError' => true, 'targetClass' => Patient::class, 'targetAttribute' => ['patient_id' => 'id']],
            [['doctor_id'], 'integer'],
            [['doctor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['doctor_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'patient_id' => 'Pasien',
            'treatment_date' => 'Tanggal Pemeriksaan',
            'diagnosis' => 'Diagnosis',
            'status' => 'Status',
            'payment_status' => 'Status Pembayaran',
            'created_at' => 'Dibuat Pada',
            'updated_at' => 'Diperbarui Pada',
            'created_by' => 'Dibuat Oleh',
            'updated_by' => 'Diperbarui Oleh',
            'doctor_id' => 'Dokter',
        ];
    }

    /**
     * Gets query for [[MedicineDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMedicineDetails()
    {
        return $this->hasMany(MedicineDetail::class, ['medical_record_id' => 'id']);
    }

    /**
     * Gets query for [[Patient]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPatient()
    {
        return $this->hasOne(Patient::class, ['id' => 'patient_id']);
    }

    /**
     * Gets query for [[Payments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPayments()
    {
        return $this->hasMany(Payment::class, ['medical_record_id' => 'id']);
    }

    /**
     * Gets query for [[TreatmentDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTreatmentDetails()
    {
        return $this->hasMany(TreatmentDetail::class, ['medical_record_id' => 'id']);
    }
    
    /**
     * Get payment status label
     */
    public function getPaymentStatusLabel()
    {
        $labels = [
            self::PAYMENT_STATUS_UNPAID => 'Belum Dibayar',
            self::PAYMENT_STATUS_PARTIAL => 'Dibayar Sebagian',
            self::PAYMENT_STATUS_PAID => 'Lunas',
        ];
        
        return isset($labels[$this->payment_status]) ? $labels[$this->payment_status] : 'Tidak Diketahui';
    }
    
    /**
     * Get status label
     */
    public function getStatusLabel()
    {
        $labels = [
            self::STATUS_ONGOING => 'Dalam Proses',
            self::STATUS_COMPLETED => 'Selesai',
            self::STATUS_WAITING_PAYMENT => 'Menunggu Pembayaran',
        ];
        
        return isset($labels[$this->status]) ? $labels[$this->status] : 'Tidak Diketahui';
    }
    
    /**
     * Calculate total treatment cost
     */
    public function getTreatmentTotal()
    {
        $total = 0;
        foreach ($this->treatmentDetails as $detail) {
            $total += $detail->getSubtotal();
        }
        return $total;
    }
    
    /**
     * Calculate total medicine cost
     */
    public function getMedicineTotal()
    {
        $total = 0;
        foreach ($this->medicineDetails as $detail) {
            $total += $detail->getSubtotal();
        }
        return $total;
    }
    
    /**
     * Calculate total invoice amount
     */
    public function getTotalAmount()
    {
        return $this->getTreatmentTotal() + $this->getMedicineTotal();
    }
    
    /**
     * Calculate paid amount
     */
    public function getPaidAmount()
    {
        $total = 0;
        foreach ($this->payments as $payment) {
            $total += $payment->amount;
        }
        return $total;
    }
    
    /**
     * Calculate remaining amount
     */
    public function getRemainingAmount()
    {
        return $this->getTotalAmount() - $this->getPaidAmount();
    }
    
    /**
     * Update payment status
     */
    public function updatePaymentStatus()
    {
        $totalAmount = $this->getTotalAmount();
        $paidAmount = $this->getPaidAmount();
        
        if ($paidAmount <= 0) {
            $this->payment_status = self::PAYMENT_STATUS_UNPAID;
        } elseif ($paidAmount < $totalAmount) {
            $this->payment_status = self::PAYMENT_STATUS_PARTIAL;
        } else {
            $this->payment_status = self::PAYMENT_STATUS_PAID;
        }
        
        return $this->save(false, ['payment_status']);
    }
    
    /**
     * Get status options for dropdown
     * @return array
     */
    public function getStatusOptions()
    {
        return [
            self::STATUS_ONGOING => 'Dalam Proses',
            self::STATUS_COMPLETED => 'Selesai',
            self::STATUS_WAITING_PAYMENT => 'Menunggu Pembayaran',
        ];
    }
}
