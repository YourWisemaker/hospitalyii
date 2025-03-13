<?php

namespace app\models;

use Yii;
use app\models\base\BaseModel;
use app\models\User;  // Add this line

/**
 * This is the model class for table "treatment".
 *
 * @property int $id
 * @property string $name
 * @property float $price
 * @property string|null $description
 * @property int $created_at
 * @property int $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 *
 * @property TreatmentDetail[] $treatmentDetails
 */
class Treatment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%treatment}}';
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
            ],
            [
                'class' => \yii\behaviors\BlameableBehavior::class,
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
        ];
    }

    public function rules()
    {
        return [
            [['name', 'price', 'code'], 'required', 'message' => '{attribute} tidak boleh kosong.'],
            [['price'], 'number', 'message' => '{attribute} harus berupa angka.'],
            [['category', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer', 'message' => '{attribute} harus berupa bilangan bulat.'],
            [['category'], 'default', 'value' => 1],
            [['description'], 'string'],
            [['name'], 'string', 'max' => 100, 'message' => '{attribute} tidak boleh lebih dari 100 karakter.'],
            [['code'], 'string', 'max' => 20, 'message' => '{attribute} tidak boleh lebih dari 20 karakter.'],
            [['name'], 'unique', 'message' => 'Nama tindakan ini sudah digunakan.'],
            [['code'], 'unique', 'message' => 'Kode tindakan ini sudah digunakan.'],
            // Ensure price is non-negative
            [['price'], 'compare', 'compareValue' => 0, 'operator' => '>=', 'message' => '{attribute} tidak boleh kurang dari 0.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'Kode',
            'name' => 'Nama Tindakan',
            'category' => 'Kategori',
            'price' => 'Biaya',
            'status' => 'Status',
            'description' => 'Deskripsi',
            'created_at' => 'Dibuat Pada',
            'updated_at' => 'Diperbarui Pada',
            'created_by' => 'Dibuat Oleh',
            'updated_by' => 'Diperbarui Oleh',
        ];
    }

    /**
     * Gets query for [[TreatmentDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTreatmentDetails()
    {
        return $this->hasMany(TreatmentDetail::class, ['treatment_id' => 'id']);
    }

    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    public function getCategoryOptions()
    {
        return [
            1 => 'Pemeriksaan Umum',
            2 => 'Tindakan Medis',
            3 => 'Prosedur Khusus',
            4 => 'Konsultasi Spesialis',
            5 => 'Perawatan Gigi',
            6 => 'Fisioterapi',
            7 => 'Laboratorium',
            8 => 'Radiologi',
            9 => 'Perawatan Luka',
            10 => 'Kebidanan',
            11 => 'Anak',
            12 => 'Penyakit Dalam',
            13 => 'Bedah',
            14 => 'Mata',
            15 => 'THT',
            16 => 'Kulit dan Kelamin',
            17 => 'Jantung',
            18 => 'Paru',
            19 => 'Saraf',
            20 => 'Jiwa',
            21 => 'Gizi',
            22 => 'Akupunktur',
            23 => 'Rehabilitasi Medik',
            24 => 'Urologi',
            25 => 'Ortopedi',
            26 => 'Onkologi',
            27 => 'Endokrinologi',
            28 => 'Hematologi',
            29 => 'Nefrologi',
            30 => 'Lainnya',
        ];
    }

    public function getCategoryLabel()
    {
        $options = $this->getCategoryOptions();
        return isset($this->category) && isset($options[$this->category]) ? $options[$this->category] : null;
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        // Ensure category and status have default values if not set
        if ($this->category === null) {
            $this->category = 1;
        }
        if ($this->status === null) {
            $this->status = 1;
        }

        // Ensure code is set if this is a new record
        if ($insert && empty($this->code)) {
            // Generate a unique code based on ID or timestamp if ID is not available yet
            $prefix = 'TDK';
            $latestTreatment = self::find()->orderBy(['id' => SORT_DESC])->one();
            $nextId = $latestTreatment ? ($latestTreatment->id + 1) : 1;
            $this->code = $prefix . str_pad($nextId, 3, '0', STR_PAD_LEFT);
            
            // Ensure the generated code is unique
            $counter = 1;
            $originalCode = $this->code;
            while (self::find()->where(['code' => $this->code])->exists()) {
                $this->code = $originalCode . $counter;
                $counter++;
            }
        }

        // Ensure price is a valid number
        if (empty($this->price) || !is_numeric($this->price)) {
            $this->price = 0;
        }

        return true;
    }

    public function afterFind()
    {
        parent::afterFind();
        // Ensure attributes are properly initialized
        $this->category = (int)$this->category;
        $this->status = (int)$this->status;
    }

    public function attributes()
    {
        $baseAttributes = (array)parent::attributes();
        $additionalAttributes = ['code', 'category', 'status'];
        
        return array_merge($baseAttributes, $additionalAttributes);
    }

    public function getStatusOptions()
    {
        return [
            1 => 'Active',
            0 => 'Inactive',
        ];
    }
}
