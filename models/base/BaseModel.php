<?php

namespace app\models\base;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * BaseModel class for common functionality
 */
class BaseModel extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
        ];
    }

    /**
     * Get list of items for dropdown
     * 
     * @param string $valueField Field to use for dropdown value
     * @param string $textField Field to use for dropdown text
     * @param string $condition Condition for filtering dropdown items
     * @return array List of items for dropdown
     */
    public static function getDropdownList($valueField = 'id', $textField = 'name', $condition = null)
    {
        $query = static::find()->select([$valueField, $textField]);
        
        if ($condition) {
            $query->where($condition);
        }
        
        return $query->orderBy($textField)->indexBy($valueField)->column();
    }
}
