<?php

namespace app\helpers;

/**
 * Helper class for safely counting variables in PHP 7.2+
 */
class CountHelper
{
    /**
     * Safely count any variable, including non-countable objects
     * 
     * @param mixed $var Variable to count
     * @return int Count result
     */
    public static function safeCount($var)
    {
        if (is_array($var) || $var instanceof \Countable) {
            return count($var);
        }
        
        if (is_object($var)) {
            return count(get_object_vars($var));
        }
        
        if ($var === null) {
            return 0;
        }
        
        if (is_string($var)) {
            return strlen($var) > 0 ? 1 : 0;
        }
        
        return 0;
    }
}
