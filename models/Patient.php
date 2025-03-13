<?php

namespace app\models;

use Yii;
use app\models\base\BaseModel;

/**
 * This is the model class for table "patient".
 *
 * @property int $id
 * @property string|null $registration_number
 * @property string $name
 * @property string|null $birth_date
 * @property string|null $gender
 * @property string|null $address
 * @property string|null $contact
 * @property string $registration_date
 * @property int $created_at
 * @property int $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 *
 * @property MedicalRecord[] $medicalRecords
 */
class Patient extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%patient}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'registration_date'], 'required'],
            [['birth_date', 'registration_date'], 'safe'],
            [['gender'], 'string'],
            [['address'], 'string'],
            [['created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['registration_number'], 'string', 'max' => 20],
            [['name'], 'string', 'max' => 100],
            [['contact'], 'string', 'max' => 15],
            [['registration_number'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'registration_number' => 'No. Registrasi',
            'name' => 'Nama',
            'birth_date' => 'Tanggal Lahir',
            'gender' => 'Jenis Kelamin',
            'address' => 'Alamat',
            'contact' => 'Kontak',
            'registration_date' => 'Tanggal Registrasi',
            'created_at' => 'Dibuat Pada',
            'updated_at' => 'Diperbarui Pada',
            'created_by' => 'Dibuat Oleh',
            'updated_by' => 'Diperbarui Oleh',
        ];
    }

    /**
     * Gets query for [[MedicalRecords]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMedicalRecords()
    {
        return $this->hasMany(MedicalRecord::class, ['patient_id' => 'id']);
    }
    
    /**
     * Generate unique registration number for new patient
     */
    public function generateRegistrationNumber()
    {
        if ($this->isNewRecord && empty($this->registration_number)) {
            $date = date('Ymd');
            $lastPatient = self::find()
                ->where(['like', 'registration_number', 'P-' . $date . '%', false])
                ->orderBy(['id' => SORT_DESC])
                ->one();
            
            $newNumber = 1;
            if ($lastPatient) {
                $lastNumber = (int)substr($lastPatient->registration_number, -4);
                $newNumber = $lastNumber + 1;
            }
            
            $this->registration_number = 'P-' . $date . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        }
    }
    
    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->generateRegistrationNumber();
            return true;
        }
        return false;
    }
    
    /**
     * Get a human-readable gender label
     * @return string
     */
    public function getGenderLabel()
    {
        $labels = [
            'M' => 'Laki-laki',
            'F' => 'Perempuan',
        ];
        
        return isset($labels[$this->gender]) ? $labels[$this->gender] : 'Tidak Diketahui';
    }
    
    /**
     * Getter for patient_number (alias for registration_number)
     */
    public function getPatient_number()
    {
        return $this->registration_number;
    }
    
    /**
     * Setter for patient_number (alias for registration_number)
     */
    public function setPatient_number($value)
    {
        $this->registration_number = $value;
    }

    public function getAge()
    {
        if ($this->birth_date) {
            $birthDate = new \DateTime($this->birth_date);
            $today = new \DateTime('today');
            $age = $birthDate->diff($today)->y;
            return $age;
        }
        return null;
    }

    /**
     * Get formatted contact number or default text if not set
     * @return string
     */
    public function getContact()
    {
        return !empty($this->contact) ? $this->contact : '(belum diset)';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => \yii\behaviors\TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => time(),
            ],
            [
                'class' => \yii\behaviors\BlameableBehavior::class,
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
        ];
    }
}
