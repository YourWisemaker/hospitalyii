<?php

namespace app\models;

use Yii;
use app\models\base\BaseModel;
use app\models\User;

/**
 * This is the model class for table "employee".
 *
 * @property int $id
 * @property string $name
 * @property string|null $position
 * @property string|null $contact
 * @property string|null $address
 * @property int|null $region_id
 * @property int $created_at
 * @property int $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 *
 * @property Region $region
 */
class Employee extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    
    // Gender constants should be defined before they are used
    const GENDER_MALE = 'M';
    const GENDER_FEMALE = 'F';
    
    // Position constants
    const POSITION_DOCTOR = 'doctor';
    const POSITION_NURSE = 'nurse';
    const POSITION_ADMIN = 'admin';
    const POSITION_PHARMACIST = 'pharmacist';

    /**
     * Get position options for dropdown
     * @return array
     */
    public function getPositionOptions()
    {
        return [
            self::POSITION_DOCTOR => 'Dokter',
            self::POSITION_NURSE => 'Perawat',
            self::POSITION_ADMIN => 'Admin',
            self::POSITION_PHARMACIST => 'Apoteker',
        ];
    }

    /**
     * Get status options for dropdown
     * @return array
     */
    public function getStatusOptions()
    {
        return [
            self::STATUS_ACTIVE => 'Aktif',
            self::STATUS_INACTIVE => 'Tidak Aktif',
        ];
    }

    /**
     * Get gender options for dropdown
     * @return array
     */
    public function getGenderOptions()
    {
        return [
            self::GENDER_MALE => 'Laki-laki',
            self::GENDER_FEMALE => 'Perempuan',
        ];
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['address'], 'string'],
            [['region_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [['position'], 'string', 'max' => 50],
            [['contact', 'phone'], 'string', 'max' => 15], // Add phone to validation
            [['region_id'], 'exist', 'skipOnError' => true, 'targetClass' => Region::class, 'targetAttribute' => ['region_id' => 'id']],
            [['employee_number'], 'string', 'max' => 20],
            [['employee_number'], 'unique'],
            [['status'], 'string'],
            [['status'], 'default', 'value' => self::STATUS_ACTIVE],
            [['status'], 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE]],
            [['gender'], 'required'],
            [['gender'], 'string', 'max' => 1],
            [['gender'], 'in', 'range' => [self::GENDER_MALE, self::GENDER_FEMALE]],
            [['email'], 'string', 'max' => 100],
            [['email'], 'email'],
            [['email'], 'unique'],
            [['birth_date'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Nama',
            'position' => 'Jabatan',
            'contact' => 'Kontak',
            'address' => 'Alamat',
            'region_id' => 'Wilayah',
            'created_at' => 'Dibuat Pada',
            'updated_at' => 'Diperbarui Pada',
            'created_by' => 'Dibuat Oleh',
            'updated_by' => 'Diperbarui Oleh',
            'employee_number' => 'No. Pegawai',
            'gender' => 'Jenis Kelamin',
            'email' => 'Email',
            'birth_date' => 'Tanggal Lahir',
        ];
    }

    /**
     * Calculate age from birth_date
     * @return int|null
     */
    public function getAge()
    {
        if ($this->birth_date) {
            $birthDate = new \DateTime($this->birth_date);
            $today = new \DateTime('today');
            return $birthDate->diff($today)->y;
        }
        return null;
    }

    /**
     * Get gender label
     * @return string
     */
    public function getGenderLabel()
    {
        $options = $this->getGenderOptions();
        return isset($options[$this->gender]) ? $options[$this->gender] : 'Tidak Diketahui';
    }

    /**
     * Get position label
     * @return string
     */
    public function getPositionLabel()
    {
        $options = $this->getPositionOptions();
        return isset($options[$this->position]) ? $options[$this->position] : 'Tidak Diketahui';
    }

    /**
     * Getter for phone (alias for contact)
     */
    /**
     * Getter for phone (alias for contact)
     * @return string|null
     */
    public function getPhone()
    {
        return $this->contact;
    }

    /**
     * Setter for phone (alias for contact)
     * @param string|null $value
     */
    public function setPhone($value)
    {
        $this->contact = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => \yii\behaviors\TimestampBehavior::class,
                'value' => time(),
            ],
        ];
    }

    /**
     * Gets query for [[Region]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegion()
    {
        return $this->hasOne(Region::class, ['id' => 'region_id']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }
}
