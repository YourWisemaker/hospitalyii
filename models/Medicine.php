<?php

namespace app\models;

use Yii;
use app\models\base\BaseModel;

/**
 * This is the model class for table "medicine".
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property int $category
 * @property string $unit
 * @property float $purchase_price
 * @property float $price
 * @property float $sell_price
 * @property int $stock
 * @property int $min_stock
 * @property int $status
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
            [['name', 'price', 'code'], 'required', 'message' => '{attribute} tidak boleh kosong.'],
            [['price', 'purchase_price', 'sell_price'], 'number', 'message' => '{attribute} harus berupa angka.'],
            [['stock', 'min_stock', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer', 'message' => '{attribute} harus berupa bilangan bulat.'],
            [['category', 'status'], 'integer', 'message' => '{attribute} harus dipilih.'],
            [['category', 'status'], 'default', 'value' => 1],
            [['description'], 'string'],
            [['name', 'unit'], 'string', 'max' => 100, 'message' => '{attribute} tidak boleh lebih dari 100 karakter.'],
            [['code'], 'string', 'max' => 20, 'message' => '{attribute} tidak boleh lebih dari 20 karakter.'],
            [['name'], 'unique', 'message' => 'Nama obat ini sudah digunakan.'],
            [['code'], 'unique', 'message' => 'Kode obat ini sudah digunakan.'],
            [['purchase_price', 'sell_price', 'min_stock'], 'default', 'value' => 0],
            [['unit'], 'default', 'value' => 'Pcs'],
            [['stock'], 'default', 'value' => 0],
            // Ensure all prices are non-negative
            [['price', 'purchase_price', 'sell_price'], 'compare', 'compareValue' => 0, 'operator' => '>=', 'message' => '{attribute} tidak boleh kurang dari 0.'],
            // Ensure all stock values are non-negative
            [['stock', 'min_stock'], 'compare', 'compareValue' => 0, 'operator' => '>=', 'message' => '{attribute} tidak boleh kurang dari 0.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'Kode Obat',
            'name' => 'Nama Obat',
            'category' => 'Kategori',
            'unit' => 'Satuan',
            'purchase_price' => 'Harga Beli',
            'price' => 'Harga',
            'sell_price' => 'Harga Jual',
            'stock' => 'Stok',
            'min_stock' => 'Stok Minimal',
            'status' => 'Status',
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
    
    /**
     * Get category options for medicine
     * 
     * @return array List of category options
     */
    public function getCategoryOptions()
    {
        return [
            1 => 'Obat Bebas',
            2 => 'Obat Bebas Terbatas',
            3 => 'Obat Keras',
            4 => 'Obat Narkotika',
            5 => 'Obat Psikotropika',
            6 => 'Obat Herbal',
            7 => 'Vitamin dan Suplemen',
            8 => 'Alat Kesehatan',
            9 => 'Lainnya',
        ];
    }
    
    /**
     * Get category label
     * 
     * @return string Category label
     */
    public function getCategoryLabel()
    {
        $options = $this->getCategoryOptions();
        return isset($this->category) && isset($options[$this->category]) ? $options[$this->category] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function attributes()
    {
        $baseAttributes = parent::attributes();
        $additionalAttributes = ['code', 'category', 'unit', 'purchase_price', 'sell_price', 'min_stock', 'status'];
        
        return array_merge($baseAttributes, $additionalAttributes);
    }
    
    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        // Ensure category has a default value if not set
        if ($this->category === null) {
            $this->category = 1;
        }
        
        // Ensure status has a default value if not set
        if ($this->status === null) {
            $this->status = 1;
        }

        // Ensure code is set if this is a new record
        if ($insert && empty($this->code)) {
            // Generate a unique code based on ID or timestamp if ID is not available yet
            $prefix = 'MED';
            $latestMedicine = self::find()->orderBy(['id' => SORT_DESC])->one();
            $nextId = $latestMedicine ? ($latestMedicine->id + 1) : 1;
            $this->code = $prefix . str_pad($nextId, 3, '0', STR_PAD_LEFT);
            
            // Ensure the generated code is unique
            $counter = 1;
            $originalCode = $this->code;
            while (self::find()->where(['code' => $this->code])->exists()) {
                $this->code = $originalCode . $counter;
                $counter++;
            }
        }

        // Ensure numeric fields have proper values
        if (empty($this->purchase_price) || !is_numeric($this->purchase_price)) {
            $this->purchase_price = 0;
        }
        
        if (empty($this->sell_price) || !is_numeric($this->sell_price)) {
            $this->sell_price = 0;
        }
        
        if (empty($this->stock) || !is_numeric($this->stock)) {
            $this->stock = 0;
        }
        
        if (empty($this->min_stock) || !is_numeric($this->min_stock)) {
            $this->min_stock = 0;
        }
        
        if (empty($this->unit)) {
            $this->unit = 'Pcs';
        }

        return true;
    }
    
    /**
     * {@inheritdoc}
     */
    public function afterFind()
    {
        parent::afterFind();
        // Ensure attributes are properly initialized
        $this->category = (int)$this->category;
        $this->status = (int)$this->status;
    }
    
    /**
     * Get status options
     * 
     * @return array List of status options
     */
    public function getStatusOptions()
    {
        return [
            1 => 'Aktif',
            0 => 'Tidak Aktif',
        ];
    }
    
    /**
     * Get status label
     * 
     * @return string Status label
     */
    public function getStatusLabel()
    {
        $options = $this->getStatusOptions();
        return isset($this->status) && isset($options[$this->status]) ? $options[$this->status] : null;
    }
}
