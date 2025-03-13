<?php

namespace app\components;

use Yii;
use yii\web\ErrorHandler;

/**
 * CustomErrorHandler extends the default Yii web error handler
 * to suppress specific warnings related to count() on non-countable objects
 */
class CustomErrorHandler extends ErrorHandler
{
    /**
     * @inheritdoc
     */
    public function handleError($code, $message, $file, $line)
    {
        // Suppress specific count() warnings
        if (strpos($message, 'count(): Parameter must be an array or an object that implements Countable') !== false) {
            return true; // Suppress this warning
        }
        
        // Handle other errors normally
        return parent::handleError($code, $message, $file, $line);
    }
}
